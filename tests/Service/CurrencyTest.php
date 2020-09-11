<?php

declare(strict_types=1);

namespace Cash\CommissionTask\Tests\Service;

use Cash\CommissionTask\Service\Currency;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    /**
     * @var Currency
     */
    private $currencyService;

    public function setUp()
    {
        $this->currencyService = new Currency();
    }

    /**
     * @param string $amount
     * @param string $currency
     * @param string $expectations
     *
     * @dataProvider dataProviderForRoundCurrencyUpToTwoDecimals
     */
    public function testRoundCurrencyUpToTwoDecimals(string $amount, string $currency, string $expectations)
    {
        $this->assertEquals(
            $expectations,
            $this->currencyService->roundCurrencyUpToSmallestItem($amount, $currency));
    }

    public function dataProviderForRoundCurrencyUpToTwoDecimals(): array
    {
        return [
            'single digit integer to EUR' => [1,'EUR', '1.00'],
            'small fractional-part to USD' => [2.800041,'USD', '2.81'],
            'big fractional-part to JPY' => [2.1999999,'JPY','3']
        ];
    }

    /**
     * @param float $amount
     * @param string $from
     * @param string $to
     * @param string $expectations
     *
     * @dataProvider dataProviderForConvertCurrency
     */
    public function testConvertCurrency(float $amount, string $from, string $to, string $expectations)
    {
        $this->assertEquals(
            $expectations,
            $this->currencyService->convertCurrency($amount, $from, $to));
    }

    public function dataProviderForConvertCurrency(): array
    {
        return [
            'EUR to USD' => [100, 'EUR', 'USD', '114.97'],
            'EUR to JPY' => [100, 'EUR', 'JPY', '12953'],
            'USD to EUR' => [100, 'USD', 'EUR', '86.97921'],
            'USD to JPY' => [100, 'USD', 'JPY', '11266.41707'],
            'JPY to EUR' => [100, 'JPY', 'EUR', '0.77202'],
            'JPY to USD' => [100, 'JPY', 'USD', '0.88759'],
        ];
    }
}