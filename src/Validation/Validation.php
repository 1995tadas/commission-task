<?php
declare(strict_types=1);

namespace Cash\CommissionTask\Validation;

class Validation
{
    /**
     * @param $message
     */
    protected function exitWithMessage($message)
    {
        exit($message);
    }

    /**
     * @param array $array
     * @param int $requiredItemCount
     */
    public function validateArraysItemNumber(array $array, int $requiredItemCount): void
    {
        if (count($array) != $requiredItemCount) {
            $this->exitWithMessage("Your array should have " . $requiredItemCount . " required items");
        }
    }

    /**
     * @param string $path
     * @param string $extension
     */
    public function validateFileByPathExists(string $path, string $extension): void
    {
        if (!(file_exists($path) && pathinfo($path)['extension'] === $extension)) {
            $this->exitWithMessage("File don't exist or not " . $extension);
        }
    }

    /**
     * @param $resource
     */
    public function validateResource($resource): void
    {
        if (!is_resource($resource)) {
            $this->exitWithMessage("Input is not resource");
        }
    }

    /**
     * @param string $date
     */
    public function validateDateYmdFormat(string $date): void
    {
        $pattern = '/^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/';
        if (!preg_match($pattern, $date)) {
            $this->exitWithMessage('"' . $date . '" date format should be yyyy-mm-dd and valid');
        }
    }

    /**
     * @param string $date
     */
    public function validateDateFromPast(string $date): void
    {
        if ($date > date("Y-m-d")) {
            $this->exitWithMessage('"' . $date . '" date is from the future');
        }
    }

    /**
     * @param string $string
     */
    public function validateStringOnlyDigits(string $string): void
    {
        if (!ctype_digit($string)) {
            $this->exitWithMessage('"' . $string . '" should be numerical string');
        }
    }

    /**
     * @param string $value
     * @param string ...$possibleValues
     */
    public function validatePossibleValues(string $value, string ...$possibleValues): void
    {
        if (!in_array($value, $possibleValues)) {
            $this->exitWithMessage('"' . $value . '" is not possible');
        }
    }

    /**
     * @param string $currency
     */
    public function validateCurrency(string $currency)
    {
        $pattern = '/^[0-9]+(\.[0-9]{1,2})?$/';
        if (!preg_match($pattern, $currency)) {
            $this->exitWithMessage('"' . $currency . '" currency format should be 00.00 or 00 ');
        }
    }
}