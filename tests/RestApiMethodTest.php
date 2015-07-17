<?php

use Okapi\Example\ExampleMethod;
use Okapi\Example\ExampleMethod2;

class RestApiMethodTest extends PHPUnit_Framework_TestCase
{
    public function testValidVersionRecognize()
    {
        $method = new ExampleMethod();

        $result = $method->run(1);

        $this->assertEquals('1.0', $result['response']);

        $result = $method->run(1.0);
        $this->assertEquals('1.0', $result['response']);

        $result = $method->run('1');
        $this->assertEquals('1.0', $result['response']);

        $result = $method->run('1.0');
        $this->assertEquals('1.0', $result['response']);

        $result = $method->run('1.3');
        $this->assertEquals('1.3', $result['response']);

        $result = $method->run('3.22');
        $this->assertEquals('3.21', $result['response']);

        $result = $method->run('3.30');
        $this->assertEquals('3.23', $result['response']);

        $result = $method->run('3.20');
        $this->assertEquals('3.0', $result['response']);

        $result = $method->run('2.20');
        $this->assertEquals('1.3', $result['response']);

        $result = $method->run('1.2');
        $this->assertEquals('1.0', $result['response']);
    }

    public function testInvalidVersionRecognize()
    {
        $method = new ExampleMethod();

        $invalidVersions = array(0, 0.0, '0.0', '0.1', -3.0, 'version', '4.0');
        $exceptionCount = 0;

        foreach($invalidVersions as $version) {

            try
            {
                echo $method->run($version);
            }
            catch (Exception $e)
            {
                $exceptionCount ++;
            }
        }

        $this->assertEquals(count($invalidVersions), $exceptionCount);
    }

    public function testDeprecatedVersionRecognize()
    {
        $this->setExpectedException('\Okapi\Exceptions\MethodDeprecatedRestException');
        
        $method = new ExampleMethod();
        $method->run('5');
    }

    public function testMethodNotExists()
    {
        $this->setExpectedException('\Okapi\Exceptions\MethodNotExistsRestException');

        $method = new ExampleMethod();
        $method->run('4.1');
    }

    public function testValidMethodArguments()
    {
        $method = new ExampleMethod2();

        $result = $method->run('1.0', [
            'id' => 20
        ]);

        $this->assertEquals(20, $result['response']);
    }

    public function testWrongMethodArguments()
    {
        $this->setExpectedException('\Okapi\Exceptions\WrongMethodArgumentsRestException');

        $method = new ExampleMethod2();
        $method->run('1.0');
    }
}