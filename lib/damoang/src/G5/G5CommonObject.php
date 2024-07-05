<?php

declare(strict_types=1);

namespace Damoang\Lib\G5;

/**
 * @implements \ArrayAccess<mixed, mixed>
 */

class G5CommonObject implements \ArrayAccess
{

    /** @var mixed[] */
    protected $data = [];

    /**
     * @var mixed[]
     * @readonly
     */
    protected $defaults = [];

    /**
     * @var array<
     *      string,
     *      'int'|'integer'|'bool'|'boolean'|'float'|'double'|'filter_bool'
     * >
     * @readonly
     */
    protected $casts = [
    ];

    /**
     * @param ?mixed[] $data
     */
    function __construct($data = [])
    {
        if (!is_array($data)) {
            $data = [];
        }

        $this->setAttrs(array_merge($this->defaults, $data));
    }

    /**
     * @param array<mixed> $attrs
     */
    protected function setAttrs(array $attrs): void
    {
        foreach ($attrs as $name => $value) {
            $this->setAttr($name, $value);
        }
    }

    /**
     * @param mixed $value
     */
    protected function setAttr(string $name, $value): void
    {
        if (is_string($value)) {
            $value = trim($value);
        }

        if (isset($this->casts[$name])) {
            switch ($this->casts[$name]) {
                case 'int':
                case 'integer':
                    $value = intval($value ?? 0);
                    break;
                case 'float':
                case 'double':
                    $value = floatval($value ?? 0);
                    break;
                case 'bool':
                case 'boolean':
                    $value = boolval($value);
                    break;
                case 'filter_bool':
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    break;
            }
        }

        $this->data[$name] = $value;
    }

    // ArrayAccess -------------------------------------------------------------
    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->setAttr($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }
}
