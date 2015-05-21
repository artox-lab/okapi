<?php namespace Okapi\Exceptions;

class MethodDeprecatedRestException extends BaseRestException
{
    public function __construct()
    {
        parent::__construct('Method deprecated in this API version');
    }
}