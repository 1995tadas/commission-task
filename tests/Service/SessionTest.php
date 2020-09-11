<?php

declare(strict_types=1);

namespace Cash\CommissionTask\Tests\Service;

use Cash\CommissionTask\Service\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    /**
     * @var Session
     */
    private $sessionService;

    public function setUp()
    {
        $this->sessionService = new Session();
    }

    /**
     * @param array $sessions
     * @param int $id
     * @param array $expectation
     *
     * @runInSeparateProcess
     *
     * @dataProvider dataProviderForSetFreeOfChangeCommissionSession
     */
    public function testSetFreeOfChangeCommissionSession(array $sessions, int $id, array $expectation)
    {
        session_unset();
        foreach ($sessions as $session) {
            $this->sessionService->setFreeOfChangeCommissionSession($session['id'], $session['date'], $session['amount'], $session['currency']);

        }
        $this->assertEquals(
            $expectation,
            $this->sessionService->refactorFreeOfChargeCommissionSession($id)
        );


    }

    public function dataProviderForSetFreeOfChangeCommissionSession(): array
    {
        return [
            'multiple cash Outs' => [
                [
                    ['id' => 1, 'date' => '2019-08-31', 'amount' => 207880.00, 'currency' => 'EUR'],
                    ['id' => 1, 'date' => '2020-08-31', 'amount' => 200.00, 'currency' => 'EUR'],
                    ['id' => 1, 'date' => '2020-08-31', 'amount' => 781, 'currency' => 'JPY'],
                    ['id' => 2, 'date' => '2020-08-31', 'amount' => 7777, 'currency' => 'JPY'],
                    ['id' => 1, 'date' => '2020-08-31', 'amount' => 999, 'currency' => 'USD'],
                    ['id' => 1, 'date' => '2020-08-31', 'amount' => 997779, 'currency' => 'USD'],
                ],
                1,
                [
                    'first' => ['amount' => 200, 'currency' => 'EUR'],
                    'second' => ['amount' => 781, 'currency' => 'JPY'],
                    'third' => ['amount' => 999, 'currency' => 'USD'],
                ]
            ],
        ];
    }

    /**
     * @param int $id
     * @param array $testSession
     * @param array $expectations
     *
     * @runClassInSeparateProcess
     *
     * @dataProvider dataProviderForRefactorFreeOfChargeCommissionSession
     */

    public function testRefactorFreeOfChargeCommissionSession(int $id, array $testSession, array $expectations)
    {
        foreach ($testSession as $session) {
            $_SESSION['user_' . $id] = [$session];
        }

        $refactoredSession = $this->sessionService->refactorFreeOfChargeCommissionSession($id);

        $this->assertEquals(
            $expectations,
            $refactoredSession
        );
    }

    public function dataProviderForRefactorFreeOfChargeCommissionSession(): array
    {
        return [
            'one cash in' => [1, [['amount' => 150, 'currency' => 'USD']], ['first' => ['amount' => 150, 'currency' => 'USD']]],
            'two cash in\'s' => [1, [
                ['amount' => 300, 'currency' => 'JPY'],
                ['amount' => 150, 'currency' => 'USD']
            ],
                ['first' => ['amount' => 150, 'currency' => 'USD']],
                'second' => ['amount' => 300, 'currency' => 'JPY']
            ],
            'three cash in\'s' => [1, [
                ['amount' => 17777, 'currency' => 'EUR'],
                ['amount' => 300, 'currency' => 'JPY'],
                ['amount' => 150, 'currency' => 'USD']
            ],
                ['first' => ['amount' => 150, 'currency' => 'USD']],
                'second' => ['amount' => 300, 'currency' => 'JPY'],
                'third' => ['amount' => 17777, 'currency' => 'EUR']
            ],
        ];
    }
}
