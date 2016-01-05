<?php

namespace VR\DataMapperBundle\DataMapper;

/**
 * Class Object
 *
 * @package VR\DataMapperBundle\DataMapper
 *
 * @author Jimmie Louis Borch
 */
class Object
{
    const MODE_REPLACE = 'replace';
    const MODE_PREPEND = 'prepend';
    const MODE_APPEND = 'append';

    /**
     * @param $key
     * @param array|object $input
     * @param $value
     * @return bool
     */
    public static function exists($key, $input, &$value = null)
    {
        list($key, $rest, $object, $next) = static::splitKey($key);
        $exists = is_object($input) ? static::propertyExists($key, $input, $new) : static::keyExists($key, $input, $new);
        if (!$exists) {
            return false;
        } elseif (!empty($rest)) {
            return static::exists($rest, $new, $value);
        } else {
            $value = $new;
            return true;
        }
    }

    /**
     * @param $key
     * @param $output
     * @param $value
     * @param string $mode
     */
    public static function assign($key, &$output, $value, $mode = self::MODE_REPLACE)
    {
        list($key, $rest, $object, $next) = static::splitKey($key);
        if ($output === null) {
            $output = $object ? new \stdClass() : array();
        }
        if (empty($rest)) {
            if ($object) {
                $output->$key = self::value($value, $mode, isset($output->$key) ? $output->$key : '');
            } else {
                $output[$key] = self::value($value, $mode, isset($output[$key]) ? $output[$key] : '');
            }
        } else {
            if ($object) {
                if (!static::propertyExists($key, $output)) {
                    if ($next) {
                        $output->$key = new \stdClass();
                    } else {
                        $output->$key = array();
                    }
                }
                static::assign($rest, $output->$key, $value);
            } else {
                if (!static::keyExists($key, $output)) {
                    if ($next) {
                        $output[$key] = new \stdClass();
                    } else {
                        $output[$key] = array();
                    }
                }
                static::assign($rest, $output[$key], $value);
            }
        }
    }

    /**
     * Splits and key of the format ...
     *      'foo.bar[0].biz[2].baz' into 'key': 'foo' and 'rest': 'bar[0].biz[2].baz'
     *      'bar[0].biz[2].baz' into 'key': 'bar' and 'rest': '0.biz[2].baz'
     *
     * @param $key
     * @return array
     */
    public static function splitKey($key)
    {
        $rest = '';
        $next = null;
        if (substr($key, 0, 1) === '.') {
            $object = false;
            $key = substr($key, 1);
        } elseif (substr($key, 0, 2) === '->') {
            $object = true;
            $key = substr($key, 2);
        } else {
            $object = false;
        }
        /**
         * Split .
         */
        $dotPos = strpos($key, '.');
        $arrowPos = strpos($key, '->');
        if ($dotPos !== false && ($arrowPos === false || $dotPos < $arrowPos)) {
            $rest = substr($key, $dotPos);
            $key = substr($key, 0, $dotPos);
            $next = false;
        } elseif ($arrowPos !== false) {
            $rest = substr($key, $arrowPos);
            $key = substr($key, 0, $arrowPos);
            $next = true;
        }
        /**
         * Split []
         */
        if (($pos = strpos($key, '[')) !== false) {
            $index = substr($key, $pos+1, -1);
            $key = substr($key, 0, $pos);
            $rest = '.' . $index . $rest;
            $next = false;
        }
        return array($key, $rest, $object, $next);
    }

    /**
     * @param $key
     * @param $input
     * @param null $value
     * @return bool
     */
    public static function propertyExists($key, $input, &$value = null)
    {
        if (strpos($key, '(') !== false) {
            preg_match('/([a-zA-Z0-9]+)\(([^\)]*)\)/', $key, $matches);
            $method = $matches[1];
            $arguments = empty($matches[2]) ? array() : explode(',', $matches[2]);
            $exists = method_exists($input, $method);
            if ($exists) {
                $value = call_user_func_array(array($input, $method), $arguments);
            }
            return $exists;
        } else {
            $exists = property_exists($input, $key);
            if ($exists) {
                $value = $input->$key;
            }
            return $exists;
        }
    }

    /**
     * @param $value
     * @param $mode
     * @param $existing
     * @return string
     */
    public static function value($value, $mode, $existing)
    {
        if ($mode === self::MODE_PREPEND) {
            return $value . $existing;
        } elseif ($mode === self::MODE_APPEND) {
            return $existing . $value;
        } else {
            return $value;
        }
    }

    /**
     * @param $key
     * @param array $input
     * @param null $value
     * @return bool
     */
    public static function keyExists($key, $input, &$value = null)
    {
        if (!is_array($input)) {
            return false;
        }
        $exists = array_key_exists($key, $input);
        if ($exists) {
            $value = $input[$key];
        }
        return $exists;
    }
}
