<?php

namespace Cash\CommissionTask\Service;

class Currency
{
    /**
     * @param string $amount
     * @param string $currency
     * @return string
     */
    public function roundCurrencyUpToSmallestItem(string $amount, string $currency): string
    {
        if ($currency === 'EUR' || $currency === 'USD') {
            $roundingTo = 100;
        } else if ($currency === 'JPY') {
            $roundingTo = 1;
        } else {
            exit('"' . $currency . '" is not supported');
        }

        $math = new Math(10);
        $roundedAmount = $math->divide(ceil($math->multiply($amount, $roundingTo)), $roundingTo);
        $roundingTo === 1 ? $roundedAmount = ceil($roundedAmount) : $roundedAmount = number_format($roundedAmount,2,'.','');
        return $roundedAmount;
    }

    /**
     * @param float $amount
     * @param string $from
     * @param string $to
     * @return float
     */
    public function convertCurrency(float $amount, string $from, string $to): float
    {
        $eurToJpyRate = 129.53;
        $eurToUsdRate = 1.1497;
        $math = new Math(5);

        switch ($from) {
            case 'EUR':
                switch ($to) {
                    case 'EUR':
                        return $amount;
                    case 'JPY':
                        return $math->multiply($amount, $eurToJpyRate);
                    case 'USD':
                        return $math->multiply($amount, $eurToUsdRate);
                    default:
                        exit('"' . $to . '" to currency not supported');
                }
            case 'JPY':
                switch ($to) {
                    case 'JPY':
                        return $amount;
                    case 'EUR':
                        return $math->divide($amount, $eurToJpyRate);
                    case 'USD':
                        return $math->multiply($math->divide($amount, $eurToJpyRate), $eurToUsdRate);
                    default:
                        exit('"' . $to . '" to currency not supported');
                }
            case 'USD':
                switch ($to) {
                    case 'USD':
                        return $amount;
                    case 'JPY':
                        return $math->multiply($math->divide($amount, $eurToUsdRate), $eurToJpyRate);
                    case 'EUR':
                        return $math->divide($amount, $eurToUsdRate);
                    default:
                        exit('"' . $to . '" to currency not supported');
                }
            default:
                exit('"' . $from . '" from currency not supported');
        }
    }
}