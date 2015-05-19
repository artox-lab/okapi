<?php namespace Okapi\Core;

use Okapi\Exceptions\InvalidVersionRestException;
use Okapi\Exceptions\MethodNotExistsRestException;

class RestApiMethod
{
    public static $name = 'restApiMethod';

    protected static $versionsConfig = [];

    public static function run($version, array $params = [])
    {
        if (is_int($version))
        {
            $version .= '.00';
        }

        if (!is_float($version))
        {
            throw new InvalidVersionRestException();
        }

        list($majorVersion, $minorVersion) = explode(".", $version);

        if (empty($majorVersion) || empty($minorVersion))
        {
            throw new InvalidVersionRestException();
        }

        for ($actualMajor = $majorVersion; $actualMajor > 0; $actualMajor--)
        {
            if (isset(static::$versionsConfig[$actualMajor]) && static::$versionsConfig[$actualMajor] === false)
            {
                throw new MethodNotExistsRestException();
            }

            if (isset(static::$versionsConfig[$actualMajor]))
            {
                $actualMinor = $minorVersion;

                foreach (static::$versionsConfig[$actualMajor] as $k => $v)
                {
                    if (intval($k) > intval($minorVersion))
                    {
                        break;
                    }

                    $actualMinor = $k;
                }

                if (
                    empty(static::$versionsConfig[$actualMajor][$actualMinor])
                    || !method_exists(get_called_class(), static::$versionsConfig[$actualMajor][$actualMinor])
                )
                {
                    throw new MethodNotExistsRestException();
                }

                return forward_static_call_array([
                    get_called_class(),
                    static::$versionsConfig[$actualMajor][$actualMinor]
                ], $params);
            }
        }

        throw new MethodNotExistsRestException();
    }
}