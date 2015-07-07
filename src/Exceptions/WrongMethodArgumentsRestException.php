<?php namespace Okapi\Exceptions;

class WrongMethodArgumentsRestException extends BaseRestException
{
    public function __construct()
    {
        parent::__construct('Wrong API Method Arguments');
    }
}