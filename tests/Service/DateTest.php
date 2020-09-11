<?php

declare(strict_types=1);

namespace Cash\CommissionTask\Tests\Service;

use Cash\CommissionTask\Service\Date;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    /**
     * @var Date
     */
    private $dateService;

    public function setUp()
    {
        $this->dateService = new Date();
    }

    /**
     * @param string $earlierDate
     * @param string $laterDate
     *
     * @dataProvider dataProviderForCheckIfDatesInTheSameWeek
     */
    public function testCheckIfDatesInTheSameWeek(string $earlierDate, string $laterDate)
    {
        $this->assertTrue(
            $this->dateService->checkIfDatesInTheSameWeek($earlierDate, $laterDate)
        );
    }

    public function dataProviderForCheckIfDatesInTheSameWeek(): array
    {
        return [
            'same date' => ['2019-02-18', '2019-02-18'],
            'first date of the week and last day of the week' => ['2020-08-31', '2020-09-06'],
        ];
    }
}
