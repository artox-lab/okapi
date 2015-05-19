<?php namespace Okapi\Exceptions;

class InvalidVersionRestException extends BaseRestException
{
    public function __construct()
    {
        parent::__construct('Wrong API Version');
    }
}