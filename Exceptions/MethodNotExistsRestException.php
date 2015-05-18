<?php namespace Okapi\Exceptions;

class MethodNotExistsRestException extends BaseRestException
{
    public function __construct()
    {
        parent::__construct('Wrong API Method');
    }
}