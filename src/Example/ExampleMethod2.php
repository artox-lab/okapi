<?php namespace Okapi\Example;

use Okapi\Core\RestApiMethod;

class ExampleMethod2 extends RestApiMethod
{
    protected $versionsConfig = [
        1 => [0]
    ];

    protected function run_1_0($id)
    {
        return $id;
    }
}