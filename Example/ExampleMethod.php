<?php namespace Okapi\Example;

use Okapi\Core\RestApiMethod;

class ExampleMethod extends RestApiMethod
{
    public static $name = 'orders';

    protected static $versionsConfig = [
        '1' => [
            '00' => 'run_1_00',
            '03' => 'run_1_03'
        ],
        '3' => [
            '00' => 'run_3_00',
            '21' => 'run_3_21',
            '22' => 'run_3_22'
        ],
        '4' => false
    ];

    protected static function run_1_00()
    {
        return '1.00';
    }

    protected static function run_1_03()
    {
        return '1.03';
    }

    protected static function run_3_00()
    {
        return '3.00';
    }

    protected static function run_3_21()
    {
        return '3.21';
    }

    protected static function run_3_22()
    {
        return '3.22';
    }
}