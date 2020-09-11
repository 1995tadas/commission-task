<?php

declare(strict_types=1);

namespace Cash\CommissionTask\Tests\Service;

use Cash\CommissionTask\Service\CashOutNatural;
use PHPUnit\Framework\TestCase;

class CashOutNaturalTest extends TestCase
{
    /**
     * @var CashOutNatural
     */
    private $cashOutNaturalService;

    public function setUp()
    {
        $this->cashOutNaturalService = new CashOutNatural(1000);
    }

    /**
     * @param float $fullAmount
     * @param float $freeOfChargeAmount
     * @param string $expectation
     *
     * @dataProvider dataProviderForCheckForFreeOfChargeAmount
     */
    public function testCheckForFreeOfChargeAmount(float $fullAmount, float $freeOfChargeAmount, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->cashOutNaturalService->checkForFreeOfChargeAmount($fullAmount, $freeOfChargeAmount)
        );
    }

    public function dataProviderForCheckForFreeOfChargeAmount(): array
    {
        return [
            'smaller than free of charge amount' => [666, 1000, '0.0'],
            'bigger than free of charge amount' => [1001, 1000, '1.0'],
        ];
    }

    /**
     * @param string $toCurrency
     * @param string $expectation
     *
     * @dataProvider dataProviderForConvertFreeOfChargeAmount
     */
    public function testConvertFreeOfChargeAmount(string $toCurrency, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->cashOutNaturalService->convertFreeOfChargeAmount($toCurrency)
        );
    }

    public function dataProviderForConvertFreeOfChargeAmount(): array
    {
        return [
            'to EUR' => ['EUR', '1000'],
            'to USD' => ['USD', '1149.70'],
            'to JPY' => ['JPY', '129530'],
        ];
    }

    /**
     * @param string $expectation
     *
     * @param array $firstSum
     * @param array $secondSum
     * @param array|null $thirdSum
     * @dataProvider dataProviderForCheckForFreeOfChargeInPreviousCashOuts
     */
    public function testConvertSumPreviousCashOuts(string $expectation, array $firstSum, array $secondSum, array $thirdSum = null)
    {
        $this->assertEquals(
            $expectation,
            $this->cashOutNaturalService->checkForFreeOfChargeInPreviousCashOuts($firstSum, $secondSum, $thirdSum)
        );
    }

    public function dataProviderForCheckForFreeOfChargeInPreviousCashOuts(): array
    {
        return [
            'sum two EUR cash outs when second are bigger' => [200, ['amount' => 100, 'currency' => 'EUR'], ['amount' => 1100, 'currency' => 'EUR']],
            'sum two mixed cash outs when first are bigger' => [1100, ['amount' => 10000, 'currency' => 'USD'], ['amount' => 1100, 'currency' => 'JPY']],
            'sum three cash outs when third are bigger' => [300, ['amount' => 100, 'currency' => 'EUR'], ['amount' => 100, 'currency' => 'EUR'], ['amount' => 1100, 'currency' => 'EUR']],
            'sum three cash outs when first two are bigger' => [140, ['amount' => 10000, 'currency' => 'EUR'], ['amount' => 10000, 'currency' => 'EUR'], ['amount' => 140, 'currency' => 'EUR']],
        ];
    }
}
