<?php

namespace Cash\CommissionTask\Service;

use Exception;

class Session
{
    /**
     * Sets data to session for natural cash out
     * Only keeps first 3 cash outs by the same user in the same week for later use
     * If there is more than three cash outs this week by the same user returns last cash out amount
     * @param int $id
     * @param string $date
     * @param float $amount
     * @param string $currency
     * @return float
     * @throws Exception
     */
    public function setFreeOfChangeCommissionSession(int $id, string $date, float $amount, string $currency)
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (!isset($_SESSION['user_' . $id])) {
            $_SESSION['user_' . $id] = [['date' => $date, 'amount' => $amount, 'currency' => $currency]];
        } else {
            $dateService = new Date();
            foreach ($_SESSION['user_' . $id] as $index => $cashOut) {
                if (!$dateService->checkIfDatesInTheSameWeek($cashOut['date'], $date)) {
                    unset($_SESSION['user_' . $id][$index]);
                }
            }
            if (count($_SESSION['user_' . $id]) < 3) {
                array_push($_SESSION['user_' . $id], ['date' => $date, 'amount' => $amount, 'currency' => $currency]);
            } else {
                return $amount;
            }
        }
    }

    /**
     * @param int $id
     * @return array
     */
    public function refactorFreeOfChargeCommissionSession(int $id): array
    {
        $refactoredFreeOfCharge = [];
        foreach ($_SESSION['user_' . $id] as $cashOut) {
            if (!isset($refactoredFreeOfCharge['first'])) {
                $refactoredFreeOfCharge['first']['amount'] = $cashOut['amount'];
                $refactoredFreeOfCharge['first']['currency'] = $cashOut['currency'];
            } else if (!isset($refactoredFreeOfCharge['second'])) {
                $refactoredFreeOfCharge['second']['amount'] = $cashOut['amount'];
                $refactoredFreeOfCharge['second']['currency'] = $cashOut['currency'];
            } else if (!isset($refactoredFreeOfCharge['third'])) {
                $refactoredFreeOfCharge['third']['amount'] = $cashOut['amount'];
                $refactoredFreeOfCharge['third']['currency'] = $cashOut['currency'];
                break;
            }
        }

        return $refactoredFreeOfCharge;
    }
}