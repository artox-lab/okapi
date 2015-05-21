<?php namespace Okapi\Core;

use Okapi\Exceptions\InvalidVersionRestException;
use Okapi\Exceptions\MethodDeprecatedRestException;
use Okapi\Exceptions\MethodNotExistsRestException;

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

        if (!method_exists(get_called_class(), $method))
        {
            throw new MethodNotExistsRestException();
        }

        return call_user_func_array([$this, $method], $params);
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