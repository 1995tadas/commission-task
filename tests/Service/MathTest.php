<?php

declare(strict_types=1);

namespace Cash\CommissionTask\Tests\Service;

use PHPUnit\Framework\TestCase;
use Cash\CommissionTask\Service\Math;

class MathTest extends TestCase
{
    /**
     * @var Math
     */
    private $mathService;

    public function setUp()
    {
        $this->mathService = new Math(2);
    }

    /**
     * @param string $leftOperand
     * @param string $rightOperand
     * @param string $expectation
     *
     * @dataProvider dataProviderForAddTesting
     */
    public function testAdd(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->mathService->add($leftOperand, $rightOperand)
        );
    }

    public function dataProviderForAddTesting(): array
    {
        return [
            'add 2 natural numbers' => ['1', '2', '3'],
            'add negative number to a positive' => ['-1', '2', '1'],
            'add natural number to a float' => ['1', '1.05123', '2.05'],
        ];
    }

    /**
     * @param string $leftOperand
     * @param string $rightOperand
     * @param string $expectation
     *
     * @dataProvider dataProviderForMultiplyTesting
     */
    public function testMultiply(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->mathService->multiply($leftOperand, $rightOperand)
        );
    }

    public function dataProviderForMultiplyTesting(): array
    {
        return [
            'multiply 2 natural numbers' => ['1', '2', '2'],
            'multiply by zero' => ['0', '2', '0'],
            'multiply negative with float' => ['-2', '6.08', '-12.16'],
        ];
    }

    /**
     * @param string $leftOperand
     * @param string $rightOperand
     * @param string $expectation
     *
     * @dataProvider dataProviderForDivideTesting
     */
    public function testDivide(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->mathService->divide($leftOperand, $rightOperand)
        );
    }

    public function dataProviderForDivideTesting(): array
    {
        return [
            'divide 2 natural numbers' => ['1', '2', '0.50'],
            'divide zero' => ['0', '2', '0'],
            'multiply negative with float' => ['-7.8', '0.84', '-9.28'],
        ];
    }

    /**
     * @param string $number
     * @param string $percentage
     * @param string $expectation
     *
     * @dataProvider dataProviderForPercentageTesting
     */
    public function testPercentage(string $number, string $percentage, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->mathService->percentage($number, $percentage)
        );
    }

    public function dataProviderForPercentageTesting(): array
    {
        return [
            'percentage of 2 natural numbers' => ['100', '1', '1'],
            'percentage of zero' => ['0', '2', '0'],
            'multiply negative with float' => ['-1200', '2.7', '-32.40'],
        ];
    }
}
