<?php

namespace Cash\CommissionTask\Controllers;

use Cash\CommissionTask\Service\CashOutNatural;
use Cash\CommissionTask\Service\Currency;
use Cash\CommissionTask\Service\Math;
use Cash\CommissionTask\Service\Session;
use Cash\CommissionTask\Validation\Validation;

class CommissionController
{
    /**
     * @var Validation
     */
    private $validationService;
    /**
     * @var Currency
     */
    private $currencyService;
    /**
     * @var Math
     */
    private $mathService;

    private $date;
    private $id;
    private $userType;
    private $type;
    private $amount;
    private $currency;

    const CASH_IN_COMMISSION_MAX = 5.00;
    const CASH_IN_COMMISSION_FEE = 0.03;
    const CASH_OUT_COMMISSION_MIN = 0.50;
    const CASH_OUT_COMMISSION_FEE = 0.3;
    const CASH_OUT_NATURAL_FREE_OF_CHARGE_AMOUNT = 1000;

    /**
     * CommissionController constructor.
     * @param string $date
     * @param int $id
     * @param string $userType
     * @param string $type
     * @param float $amount
     * @param string $currency
     */
    public function __construct(string $date, int $id, string $userType, string $type, float $amount, string $currency)
    {
        $this->validationService = new Validation();
        $this->currencyService = new Currency();
        $this->mathService = new Math(6);

        $this->setDate($date);
        $this->setId($id);
        $this->setUserType($userType);
        $this->setType($type);
        $this->setAmount($amount);
        $this->setCurrency($currency);
    }

    /**
     * @param string $date
     */
    protected function setDate(string $date): void
    {
        $this->validationService->validateDateYmdFormat($date);
        $this->validationService->validateDateFromPast($date);
        $this->date = $date;
    }

    /**
     * @param string $id
     */
    protected function setId(string $id): void
    {
        $this->validationService->validateStringOnlyDigits($id);
        $this->id = $id;
    }

    /**
     * @param string $userType
     */
    protected function setUserType(string $userType): void
    {
        $this->validationService->validatePossibleValues($userType, 'natural', 'legal');
        $this->userType = $userType;
    }

    /**
     * @param string $type
     */
    protected function setType(string $type): void
    {
        $this->validationService->validatePossibleValues($type, 'cash_in', 'cash_out');
        $this->type = $type;
    }

    /**
     * @param string $amount
     */
    protected function setAmount(string $amount): void
    {
        $this->validationService->validateCurrency($amount);
        $this->amount = $amount;
    }

    /**
     * @param string $currency
     */
    protected function setCurrency(string $currency): void
    {
        $this->validationService->validatePossibleValues($currency, 'EUR', 'USD', 'JPY');
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getCommissionFee(): string
    {
        if ($this->type === 'cash_in') {
            $commissionFee = $this->cashIn();

        } else if ($this->type === 'cash_out') {
            $commissionFee = $this->cashOut();
        }
        if (isset($commissionFee)) {
            return $this->currencyService->roundCurrencyUpToSmallestItem($commissionFee, $this->currency);
        }
    }

    /**
     * @return string
     */
    protected function cashIn(): string
    {
        $commissionFee = $this->mathService->percentage($this->amount, self::CASH_IN_COMMISSION_FEE);
        $maxCommissionFee = $this->currencyService->convertCurrency(self::CASH_IN_COMMISSION_MAX, 'EUR', $this->currency);
        return $commissionFee <= $maxCommissionFee ? $commissionFee : $maxCommissionFee;
    }

    /**
     * @return string
     */
    protected function cashOut(): string
    {
        if ($this->userType === 'legal') {
            return $this->legal();
        } else if ($this->userType === 'natural') {
            return $this->natural();
        }
    }

    /**
     * @return string
     */
    protected function legal(): string
    {
        $commissionFee = $this->mathService->percentage($this->amount, self::CASH_OUT_COMMISSION_FEE);
        $minimalCommissionFee = $this->currencyService->convertCurrency(self::CASH_OUT_COMMISSION_MIN, 'EUR', $this->currency);
        return $commissionFee > $minimalCommissionFee ? $commissionFee : $minimalCommissionFee;
    }

    /**
     * @return string
     */
    protected function natural(): string
    {
        $sessionService = new Session();
        $freeOfChargeNotApplicable = $sessionService->setFreeOfChangeCommissionSession($this->id, $this->date, $this->amount, $this->currency);
        if ($freeOfChargeNotApplicable) {
            $sum = $freeOfChargeNotApplicable;
        } else {
            $refactoredCashOut = $sessionService->refactorFreeOfChargeCommissionSession($this->id);
            $CashOutNaturalService = new CashOutNatural(self::CASH_OUT_NATURAL_FREE_OF_CHARGE_AMOUNT);
            if (array_key_exists('third', $refactoredCashOut)) {
                $sum = $CashOutNaturalService->checkForFreeOfChargeInPreviousCashOuts($refactoredCashOut['first'], $refactoredCashOut['second'], $refactoredCashOut['third']);
            } else if (array_key_exists('second', $refactoredCashOut)) {
                $sum = $CashOutNaturalService->checkForFreeOfChargeInPreviousCashOuts($refactoredCashOut['first'], $refactoredCashOut['second']);
            } else if (array_key_exists('first', $refactoredCashOut)) {
                $sum = $CashOutNaturalService->checkForFreeOfChargeAmount($refactoredCashOut['first']['amount'], $CashOutNaturalService->convertFreeOfChargeAmount($refactoredCashOut['first']['currency']));
            }
        }

        return $this->mathService->percentage($sum, self::CASH_OUT_COMMISSION_FEE);
    }
}