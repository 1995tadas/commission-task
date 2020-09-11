<?php

declare(strict_types=1);

namespace Cash\CommissionTask\Tests\Service;

use Cash\CommissionTask\Controllers\CommissionController;
use Cash\CommissionTask\Service\Session;
use PHPUnit\Framework\TestCase;

class CommissionControllerTest extends TestCase
{

    /**
     * @param array $input
     * @param string $expectations
     *
     * @dataProvider dataProviderForGetCommissionFeeCashIn
     * @dataProvider dataProviderForGetCommissionFeeCashOutLegal
     */

    public function testCashInCommissionFee(array $input, string $expectations)
    {

        $commissionController = new CommissionController(...$input);
        $this->assertEquals(
            $expectations,
            $commissionController->getCommissionFee());
    }

    public function dataProviderForGetCommissionFeeCashIn(): array
    {
        return [
            'Input data EUR' => [['2016-01-06', 2, 'natural', 'cash_in', 500.00, 'EUR'], '0.15'],
            'Input data JPY' => [['2016-01-06', 2, 'legal', 'cash_in', 500000, 'JPY'], '150'],
            'Input data USD' => [['2016-01-06', 2, 'legal', 'cash_in', 5000.00, 'USD'], '1.5'],
            'MAX commission fee EUR' => [['2016-01-06', 2, 'legal', 'cash_in', 50000.00, 'EUR'], '5.00'],
            'MAX commission fee JPY' => [['2016-01-06', 2, 'natural', 'cash_in', 50000000, 'JPY'], '648'],
            'MAX commission fee USD' => [['2016-01-06', 2, 'legal', 'cash_in', 44000.00, 'USD'], '5.75'],
            'smallest possible amount EUR' => [['2016-01-06', 2, 'legal', 'cash_in', 0.01, 'EUR'], '0.01'],
            'smallest possible amount JPY' => [['2016-01-06', 2, 'legal', 'cash_in', 0.01, 'JPY'], '1'],
            'smallest possible amount USD' => [['2016-01-06', 2, 'natural', 'cash_in', 0.01, 'USD'], '0.01'],
        ];
    }

    public function dataProviderForGetCommissionFeeCashOutLegal(): array
    {
        return [
            'Input data EUR' => [['2016-01-06', 2, 'legal', 'cash_out', 5000.00, 'EUR'], '15.00'],
            'Input data JPY' => [['2016-01-06', 2, 'legal', 'cash_out', 500000, 'JPY'], '1500'],
            'Input data USD' => [['2016-01-06', 2, 'legal', 'cash_out', 50000.00, 'USD'], '150.00'],
            'MIN commission fee EUR' => [['2016-01-06', 2, 'legal', 'cash_out', 69.00, 'EUR'], '0.50'],
            'MIN commission fee JPY' => [['2016-01-06', 2, 'legal', 'cash_out', 6969, 'JPY'], '65'],
            'MIN commission fee USD' => [['2016-01-06', 2, 'legal', 'cash_out', 169.00, 'USD'], '0.58'],
        ];
    }

    /**
     * @param array $input
     * @param array $previousCashOuts
     * @param string $expectations
     *
     * @dataProvider dataProviderForGetCommissionFeeCashOutNatural
     */

    public function testCashOutNaturalCommissionFee(array $input, array $previousCashOuts, string $expectations)
    {
        $sessionService = new Session();
        foreach ($previousCashOuts as $previousCashOut) {
            $sessionService->setFreeOfChangeCommissionSession(...$previousCashOut);
        }

        $commissionController = new CommissionController(...$input);
        $this->assertEquals(
            $expectations,
            $commissionController->getCommissionFee());
    }

    public function dataProviderForGetCommissionFeeCashOutNatural(): array
    {
        $previousCashOuts[1] = [[5, '2018-12-31', 900.00, 'EUR'], [5, '2019-01-06', 100.00, 'EUR']];
        $previousCashOuts[2] = [[6, '2018-12-31', 500.00, 'EUR'], [6, '2019-01-06', 10000.00, 'USD']];
        $previousCashOuts[3] = [[7, '2018-12-31', 100.00, 'EUR'], [7, '2019-01-06', 100.00, 'USD']];
        $previousCashOuts[4] = [[8, '2018-12-31', 800.00, 'EUR'], [8, '2019-01-06', 100.00, 'USD']];
        $previousCashOuts[5] = [[9, '2019-01-06', 1000.00, 'USD']];
        $previousCashOuts[6] = [[10, '2019-01-06', 900.00, 'EUR']];
        $previousCashOuts[7] = [[11, '2019-01-06', 14000, 'JPY']];
        $previousCashOuts[8] = [[12, '2019-01-06', 100.00, 'JPY'], [12, '2019-01-06', 1400, 'JPY'], [12, '2019-01-06', 14.00, 'USD']];
        return [
            'Free of charge exceeded in single cash out EUR' => [['2019-01-06', 2, 'natural', 'cash_out', 2000.00, 'EUR'], [], '3.00'],
            'Free of charge exceeded in single cash out USD' => [['2019-01-06', 3, 'natural', 'cash_out', 2000.00, 'USD'], [], '2.56'],
            'Free of charge exceeded in single cash out JPY' => [['2019-01-06', 4, 'natural', 'cash_out', 150000, 'JPY'], [], '62'],
            'Free of charge exceeded in three cash outs EUR ' => [['2019-01-06', 5, 'natural', 'cash_out', 100.00, 'EUR'], $previousCashOuts[1], '0.30'],
            'Free of charge exceeded in three cash outs mixed' => [['2019-01-06', 6, 'natural', 'cash_out', 150000, 'JPY'], $previousCashOuts[2], '450'],
            'Free of charge not exceeded in three cash outs EUR ' => [['2019-01-06', 7, 'natural', 'cash_out', 100.00, 'EUR'], $previousCashOuts[3], '0.00'],
            'Free of charge not exceeded in three cash outs USD ' => [['2019-01-06', 8, 'natural', 'cash_out', 100.00, 'USD'], $previousCashOuts[4], '0.00'],
            'Free of charge exceeded in two cash outs mixed' => [['2019-01-06', 9, 'natural', 'cash_out', 100000, 'JPY'], $previousCashOuts[5], '250'],
            'Free of charge exceeded in two cash outs EUR' => [['2019-01-06', 10, 'natural', 'cash_out', 600.00, 'EUR'], $previousCashOuts[6], '1.50'],
            'Free of charge not exceeded in two cash outs mixed' => [['2019-01-06', 11, 'natural', 'cash_out', 600.00, 'EUR'], $previousCashOuts[7], '0.00'],
            'Free of charge not applicable more than three cash outs ' => [['2019-01-06', 12, 'natural', 'cash_out', 100.00, 'USD'], $previousCashOuts[8], '0.30'],
        ];

    }
}