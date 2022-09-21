<?php

namespace App\Exceptions;

use Exception;

class WrongNumberOfFieldsForOrderingException extends Exception
{
    public function __construct($given, $expected)
    {
        parent::__construct("Wrong number of fields passed for ordering. {$given} given, {$expected} expected.");
    }
}
