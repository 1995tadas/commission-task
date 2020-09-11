<?php

namespace Cash\CommissionTask\Controllers;

use Cash\CommissionTask\Validation\Validation;

class FileController
{
    /**
     * @var Validation
     */
    private $validation;
    private $path;

    /**
     * FileController constructor.
     * @param string $argv
     */
    public function __construct(string $argv)
    {
        $this->validation = new Validation;
        $this->path = $argv;
    }

    /**
     * @param $file
     */
    private function getFileContent($file): void
    {
        while (($clientInputData = fgetcsv($file, 255)) !== false) {
            $this->validation->validateArraysItemNumber($clientInputData, 6);
            $commissionController = new CommissionController(...$clientInputData);
            fwrite(STDOUT, $commissionController->getCommissionFee() . PHP_EOL);
        }
    }

    public function readCsvFile(): void
    {
        $this->validation->validateFileByPathExists($this->path, 'csv');
        $file = fopen($this->path, "r");
        $this->validation->validateResource($file);
        $this->getFileContent($file);
        fclose($file);
    }
}