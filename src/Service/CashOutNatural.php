<?php

namespace Cash\CommissionTask\Service;

class CashOutNatural
{
    private $freeOfChargeAmount;

    /**
     * CashOutNatural constructor.
     * @param $freeOfChargeAmount
     */
    public function __construct($freeOfChargeAmount)
    {
        $this->freeOfChargeAmount = $freeOfChargeAmount;
    }

    /**
     * @param float $fullAmount
     * @param float $freeOfChargeAmount
     * @return float
     */
    public function checkForFreeOfChargeAmount(float $fullAmount, float $freeOfChargeAmount): float
    {
        if ($fullAmount > $freeOfChargeAmount) {
            return $fullAmount - $freeOfChargeAmount;
        } else {
            return 0.00;
        }
    }

    /**
     * @param string $toCurrency
     * @return float
     */
    public function convertFreeOfChargeAmount(string $toCurrency): float
    {
        $currencyService = new Currency();
        return $currencyService->convertCurrency($this->freeOfChargeAmount, 'EUR', $toCurrency);
    }

    /**
     * Checks if last three or two cash outs bigger than free of charge amount
     * if true return last cash out amount
     * else sums all amounts and 0 or sum - charge amount
     *
     * @param array $firstAmount
     * @param array $secondAmount
     * @param array|null $thirdAmount
     * @return string
     */
    public function checkForFreeOfChargeInPreviousCashOuts(array $firstAmount, array $secondAmount, array $thirdAmount = null): string
    {
        $math = new Math(6);
        $currencyService = new Currency();
        if (isset($thirdAmount)) {
            $thirdSumFreeOfCharge = $this->convertFreeOfChargeAmount($thirdAmount['currency']);
            $firstTwoSum = $math->add($currencyService->convertCurrency($firstAmount['amount'], $firstAmount['currency'], $thirdAmount['currency']),
                $currencyService->convertCurrency($secondAmount['amount'], $secondAmount['currency'], $thirdAmount['currency']));
            if ($this->checkForFreeOfChargeAmount($firstTwoSum, $thirdSumFreeOfCharge)) {
                return $thirdAmount['amount'];
            } else {
                return $this->checkForFreeOfChargeAmount($math->add($firstTwoSum, $thirdAmount['amount']), $thirdSumFreeOfCharge);
            }
        }

        if ($this->checkForFreeOfChargeAmount($firstAmount['amount'], $this->convertFreeOfChargeAmount($firstAmount['currency']))) {
            return $secondAmount['amount'];
        } else {
            return $this->checkForFreeOfChargeAmount($math->add($currencyService->convertCurrency($firstAmount['amount'],
                $firstAmount['currency'], $secondAmount['currency']), $secondAmount['amount']), $this->convertFreeOfChargeAmount($secondAmount['currency']));
        }
    }
}