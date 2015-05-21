<?php namespace Okapi\Example;

use Okapi\Core\RestApiMethod;

class ExampleMethod extends RestApiMethod
{
    protected $versionsConfig = [
        1 => [0, 3],
        3 => [0, 21, 23],
        4 => [1],
        5 => false
    ];

    protected function run_1_0()
    {
        return '1.0';
    }

    protected function run_1_3()
    {
        return '1.3';
    }

    protected function run_3_0()
    {
        return '3.0';
    }

    protected function run_3_21()
    {
        return '3.21';
    }

    protected function run_3_23()
    {
        return '3.23';
    }
}