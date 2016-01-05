<?php

namespace VR\DataMapperBundle\DataMapper;

include __DIR__ . '/functions.php';

/**
 * Class MapElement
 *
 * @package VR\DataMapper
 *
 * @author Jimmie Louis Borch
 */
class MapElement
{
    const KEY_SOURCE = 'source';
    const KEY_TARGET = 'target';
    const KEY_FORMAT = 'format';
    const KEY_MAPPING = 'mapping';
    const KEY_MODE = 'mode';
    const KEY_DEFAULT = 'default';

    const FORMAT_STRING = 'string';
    const FORMAT_INTEGER = 'integer';
    const FORMAT_FLOAT = 'float';
    const FORMAT_BOOLEAN = 'boolean';
    const FORMAT_MAPPING = 'mapping';
    const FORMAT_DATETIME = 'datetime';
    const FORMAT_ARRAY = 'array';
    const FORMAT_FUNCTION = 'function';
    const DEFAULT_FORMAT = self::FORMAT_STRING;

    const DATE_MAPPING_INPUT_KEY = 'input-format';
    const DATE_MAPPING_OUTPUT_KEY = 'output-format';

    const DATE_CHECK_KEY = 'date-check';

    const FLOAT_MAPPING_TS_KEY = 'thousand-separator';
    const FLOAT_MAPPING_CS_KEY = 'comma-separator';

    public $source;
    public $target;
    public $format;
    public $mapping;
    public $default;
    public $dateCheck;

    public $mode = Object::MODE_REPLACE;

    /**
     * @var array
     */
    public $availableFormats = array(
        self::FORMAT_STRING,
        self::FORMAT_INTEGER,
        self::FORMAT_FLOAT,
        self::FORMAT_BOOLEAN,
        self::FORMAT_MAPPING,
        self::FORMAT_DATETIME,
        self::FORMAT_ARRAY,
        self::FORMAT_FUNCTION
    );

    /**
     * @param string $source
     * @param string $target
     * @param string $format
     * @param array $mapping
     * @param mixed $default
     * @param mixed $dateCheck
     */
    public function __construct(
        $source,
        $target,
        $format = self::DEFAULT_FORMAT,
        array $mapping = array(),
        $default = null,
        $dateCheck = false
    ) {
        $this->source = $source;
        $this->target = $target;
        $this->setFormat($format);
        $this->setMapping($mapping);
        $this->setDefault($default);
        $this->setDateCheck($dateCheck);
    }

    /**
     * @param string $format
     */
    public function setFormat($format)
    {
        if (!method_exists($this, 'format' . ucfirst($format))) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Mapping element for source attribute \'%s\' is invalid. Format be of one of the types \'%s\', \'%s\' given.',
                    $this->source,
                    implode(',', $this->availableFormats),
                    $format
                )
            );
        }
        $this->format = $format;
    }

    /**
     * @param array $mapping
     */
    public function setMapping(array $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function setDateCheck($dateCheck)
    {
        $this->dateCheck = $dateCheck;

        return $this;
    }

    public function getDateCheck()
    {
        return $this->dateCheck;
    }

    /**
     * @param $value
     * @return mixed|integer|string
     */
    public function encode($value)
    {
        $method = 'format' . ucfirst($this->format);
        if (method_exists($this, $method)) {
            return $this->$method($value);
        }
        return $value;
    }

    /**
     * @param array|object $input
     * @param array|object $output
     */
    public function map($input, &$output)
    {
        if ($this->source === null) {
            $value = $this->encode($this->default);
        } elseif (Object::exists($this->source, $input, $value)) {
            $value = $this->encode($value);
        } else {
            $value = $this->default;
        }
        Object::assign($this->target, $output, $value, $this->mode);
    }

    /**
     * @param $value
     * @return string
     */
    public function formatString($value)
    {
        return (string) $value;
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function formatInteger($value)
    {
        if (($integer = filter_var($value, FILTER_VALIDATE_INT)) !== false) {
            return $integer;
        }
        return $this->default;
    }

    /**
     * @param $value
     * @return string
     */
    public function formatFloat($value)
    {
        if (!Object::keyExists(static::FLOAT_MAPPING_TS_KEY, $this->mapping, $tSeparator) ||
            !Object::keyExists(static::FLOAT_MAPPING_CS_KEY, $this->mapping, $cSeparator)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Source element \'%\' is of type float, and thus the mapping must contain attributes \'%\' and \'%\'.',
                    $this->source,
                    static::FLOAT_MAPPING_TS_KEY,
                    static::FLOAT_MAPPING_CS_KEY
                )
            );
        }
        return floatval(strtr($value, array($tSeparator => '', $cSeparator => '.')));
    }

    /**
     * @param $value
     * @return bool
     */
    public function formatBoolean($value)
    {
        return boolval($value);
    }

    /**
     * @param $value
     * @return string
     */
    public function formatDatetime($value)
    {
        if (!Object::keyExists(static::DATE_MAPPING_INPUT_KEY, $this->mapping, $inputFormat) ||
            !Object::keyExists(static::DATE_MAPPING_OUTPUT_KEY, $this->mapping, $outputFormat)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Source element \'%\' is of type date, and thus the mapping must contain attributes \'%\' and \'%\'.',
                    $this->source,
                    static::DATE_MAPPING_INPUT_KEY,
                    static::DATE_MAPPING_OUTPUT_KEY
                )
            );
        }
        $date = \DateTime::createFromFormat($inputFormat, $value);
        if ($date === false) {
            return $this->default;
        }
        return $date->format($outputFormat);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function formatMapping($value)
    {
        if (!Object::keyExists($value, $this->mapping, $resolved)) {
            return $this->default;
        } else {
            return $resolved;
        }
    }

    /**
     * @param $array
     * @return array
     */
    public function formatArray($array)
    {
        $map = new Map($this->mapping);
        $dataMapper = new DataMapper();
        $dataMapper->setMap($map);
        $result = array();
        foreach ($array as $value) {
            $result[] = $dataMapper->map($value);
        }
        return $result;
    }

    public function allowedFunctions()
    {
        return array(
            'date',
            'trim',
            'substr',
            'time',
            'strtotime',
            'utf8_encode',
            'utf8_decode',
            'addcslashes',
            'addslashes',
            'sprintf',
            'ltrim',
            'rtrim',
            'strtr',
            'ucfirst',
            'ucwords',
            'strtolower',
            'strtoupper',
            'htmlspecialchars',
            'htmlentities',
            'html_entity_decode',
            'regex'
        );
    }

    public function formatFunction($value)
    {
        if (!Object::keyExists('function', $this->mapping, $function) ||
            !Object::keyExists('arguments', $this->mapping, $arguments)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Source element \'%\' is of type function, and thus the mapping must contain attributes \'%\' and \'%\'.',
                    $this->source,
                    'function',
                    'arguments'
                )
            );
        }
        if (!in_array($function, $this->allowedFunctions())) {
            throw new \InvalidArgumentException(
                sprintf('Function \'%\' for source element \'%\' invalid.', $function, $this->source)
            );
        }
        $arguments = is_array($arguments) ? $arguments : array($arguments);
        array_walk_recursive($arguments, function (&$item, $key) use ($value) {
            if (is_string($item) && strpos($item, '{value}') !== false) {
                $item = strtr($item, array('{value}' => $value));
            }
        });
        return call_user_func_array($function, $arguments);
    }
}
