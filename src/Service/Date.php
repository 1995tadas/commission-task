<?php

namespace Cash\CommissionTask\Service;

use DateTime;
use Exception;

class Date
{
    /**
     * @param string $earlierDate
     * @param string $laterDate
     * @return bool
     * @throws Exception
     */
    public function checkIfDatesInTheSameWeek(string $earlierDate, string $laterDate): bool
    {
        return date('N', strtotime($earlierDate)) <= date('N', strtotime($laterDate))
            && date_diff(new DateTime($earlierDate), new DateTime($laterDate))->format("%a") < 7;
    }
}