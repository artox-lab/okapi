<?php namespace Okapi\Core;

use Okapi\Exceptions\InvalidVersionRestException;
use Okapi\Exceptions\MethodDeprecatedRestException;
use Okapi\Exceptions\MethodNotExistsRestException;
use Okapi\Exceptions\WrongMethodArgumentsRestException;

abstract class RestApiMethod
{
    const METHOD_MASK = 'run_%d_%d';

    protected $versionsConfig = [];

    /**
     * @param string $version
     * @param array $params
     *
     * @return mixed
     * @throws InvalidVersionRestException
     * @throws MethodDeprecatedRestException
     * @throws MethodNotExistsRestException
     */
    public function run($version, array $params = [])
    {
        if (strpos($version, '.') === false)
        {
            $version .= '.0';
        }

        list($majorVersion, $minorVersion) = explode('.', $version);

        $actualMajor = $this->getClosestVersion($majorVersion, array_keys($this->versionsConfig));

        if ($actualMajor === false)
        {
            throw new InvalidVersionRestException();
        }

        // Is Deprecated?
        if ($this->versionsConfig[$actualMajor] === false)
        {
            throw new MethodDeprecatedRestException();
        }

        $actualMinor = $actualMajor == $majorVersion ?
            $this->getClosestVersion($minorVersion, $this->versionsConfig[$actualMajor]) : end($this->versionsConfig[$actualMajor]);

        if ($actualMinor === false)
        {
            throw new InvalidVersionRestException();
        }

        $method = $this->getMethodName($actualMajor, $actualMinor);

        return $this->runMethod($method, $params);
    }

    /**
     * @param $methodName
     * @param $arguments
     *
     * @return mixed
     * @throws MethodNotExistsRestException
     * @throws WrongMethodArgumentsRestException
     */
    private function runMethod($methodName, $arguments)
    {
        if (!method_exists(get_called_class(), $methodName))
        {
            throw new MethodNotExistsRestException();
        }

        $reflector = new \ReflectionMethod(get_called_class(), $methodName);
        $params = $reflector->getParameters();
        $values = [];

        foreach ($params as $param)
        {
            $name = $param->getName();
            $isArgumentGiven = array_key_exists($name, $arguments);
            if (!$isArgumentGiven && !$param->isDefaultValueAvailable())
            {
                throw new WrongMethodArgumentsRestException();
            }

            $values[$param->getPosition()] =
                $isArgumentGiven ? $arguments[$name] : $param->getDefaultValue();
        }

        return call_user_func_array([$this, $methodName], $values);
    }

    /**
     * @param double $majorVersion
     * @param double $minorVersion
     *
     * @return string
     */
    private function getMethodName($majorVersion, $minorVersion)
    {
        return sprintf(self::METHOD_MASK, $majorVersion, $minorVersion);
    }

    /**
     * @param int $version
     * @param array $versionArray
     *
     * @return int|bool
     */
    private function getClosestVersion($version, array $versionArray)
    {
        $returnVersion = false;

        foreach ($versionArray as $v)
        {
            if ($v > $version)
            {
                break;
            }
            else
            {
                $returnVersion = $v;
            }
        }

        return $returnVersion;
    }
}