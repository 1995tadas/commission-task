<?php

declare(strict_types=1);

namespace Cash\CommissionTask\Tests\Service;

use PHPUnit\Framework\TestCase;
use Cash\CommissionTask\Validation\Validation;

class ValidationTest extends TestCase
{
    /**
     * @var Validation
     */
    private $validationService;

    public function setUp()
    {
        $this->validationService = new Validation();
    }

    /**
     * @param array $array
     * @param int $itemsCount
     *
     * @dataProvider dataProviderForValidateArraysItemNumber
     */
    public function testValidateArraysItemNumber(array $array, int $itemsCount)
    {
        $this->assertNull($this->validationService->validateArraysItemNumber($array, $itemsCount));
    }

    public function dataProviderForValidateArraysItemNumber(): array
    {
        return [
            'integer' => [[1, 2, 78, 4, 971], 5],
            'mixed array' => [[1, 'test', ['a', 'b', 'c']], 3],
            'empty array' => [[], 0],
        ];
    }

    /**
     * @param string $path
     * @param string $extension
     *
     * @dataProvider dataProviderForValidateFileByPathExists
     */
    public function testValidateFileByPathExists(string $path, string $extension)
    {
        $this->assertNull($this->validationService->validateFileByPathExists($path, $extension));
    }

    public function dataProviderForValidateFileByPathExists(): array
    {
        return [
            'root file' => ['index.php', 'php'],
            'path file' => ['src/Validation/Validation.php', 'php'],
            'double // path' => ['src//Validation//Validation.php', 'php'],
        ];
    }

    /**
     * @param string $date
     *
     * @dataProvider dataProviderForValidateDateYmdFormat
     */
    public function testValidateDateYmdFormat(string $date)
    {
        $this->assertNull($this->validationService->validateDateYmdFormat($date));
    }

    public function dataProviderForValidateDateYmdFormat(): array
    {
        return [
            'current year' => ['2020-12-14'],
            'future date' => ['2027-01-12'],
            'past date' => ['1997-07-10'],
        ];
    }

    /**
     * @param string $date
     *
     * @dataProvider dataProviderForValidateDateFromPast
     */
    public function testValidateDateFromPast(string $date)
    {
        $this->assertNull($this->validationService->validateDateFromPast($date));
    }

    public function dataProviderForValidateDateFromPast(): array
    {
        return [
            'current year' => ['2020-09-01'],
            'near past date' => ['2017-01-12'],
            'past date' => ['1997-07-10'],
        ];
    }

    /**
     * @param string $string
     *
     * @dataProvider dataProviderForValidateStringOnlyDigits
     */
    public function testValidateStringForOnlyDigits(string $string)
    {
        $this->assertNull($this->validationService->validateStringOnlyDigits($string));
    }

    public function dataProviderForValidateStringOnlyDigits(): array
    {
        return [
            'current year' => ['2020'],
            'near past date' => ['2087770148812'],
            'past date' => ['118115151547'],
        ];
    }

    /**
     * @param string $value
     * @param array $possibleValues
     *
     * @dataProvider dataProviderForValidatePossibleValues
     */
    public function testValidatePossibleValues(string $value, string ...$possibleValues)
    {
        $this->assertNull($this->validationService->validatePossibleValues($value, ...$possibleValues));
    }

    public function dataProviderForValidatePossibleValues(): array
    {
        return [
            'integer type strings' => ['20', '20', '14', '48487'],
            'strings ' => ['test', 'production', 'development', 'test'],
            'long strings' => ['hi', 'hello', 'howdy', 'what\'s up', 'good morning', 'hi', 'bye'],
        ];
    }

    /**
     * @param string $currency
     *
     * @dataProvider dataProviderForValidateCurrency
     */
    public function testValidateCurrency(string $currency)
    {
        $this->assertNull($this->validationService->validateCurrency($currency));
    }

    public function dataProviderForValidateCurrency(): array
    {
        return [
            'currency with a floating point' => ['20.00'],
            'integer' => ['0'],
        ];
    }
}
