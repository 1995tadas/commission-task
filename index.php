<?php

require __DIR__ . '/vendor/autoload.php';

use Cash\CommissionTask\Controllers\FileController;
use Cash\CommissionTask\Validation\Validation;

$validation = new Validation();
$validation->validateArraysItemNumber($argv, 2);
$file = new FileController($argv[1]);
$file->readCsvFile();