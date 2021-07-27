<?php
namespace Vendimia\DataContainer;

use ReflectionClass;
use ReflectionProperty;
use ArrayAccess;
use Iterator;
use LogicException;

/**
 * Simple local DTO implementation.
 * 
 * Classes implementing this abstract class disallows setting undeclared
 * properties, have a simple validation method self::isComplete() which
 * return true if none of the properties have a null value, and a 
 * self::asArray() method which returns an array containing the all the 
 * properties and its values.
 * 
 * Properties also can be accessed as array indexes.
 * 
 * All the properties in the extended class must be declared public.
 * 
 * @author Oliver Etchebarne <yo@drmad.org>
 */
abstract class DataContainer implements ArrayAccess, Iterator
{
    private $properties = [];

    public function __construct(...$data)
    {
        $reflection = new ReflectionClass($this);

        // Obtenemos el nombre de las propiedades para uso futuro
        foreach ($reflection->getProperties(
            ReflectionProperty::IS_PUBLIC
        ) as $property) {
            $this->properties[] = $property->name;
        }

        $this->fill($data);
    }

    /**
     * Sets properties in batch
     */
    public function fill(array $args)
    {
        foreach ($args as $property => $value) {
            $this->$property = $value;
        }
    }

    /**
     * Returns if all non-optional fields are not null.
     */
    public function isComplete()
    {
        foreach ($this->properties as $property) {
            if (is_null($this->$property)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Syntax sugar for !self::isComplete()
     * @see isComplete()
     */
    public function notComplete()
    {
        return !$this->isComplete();
    }

    /**
     * Returns the required fields with null value.
     */
    public function missingFields()
    {
        $res = [];
        foreach ($this->properties as $field) {
            if (is_null($this->$field)) {
                $res[] = $field;
            }
        }

        return $res;
    }

    /**
     * Magic method to avoid setting undeclared properties.
     */
    public function __set($name, $value)
    {
        throw new LogicException("Trying to set undeclared property '$name'.");
    }

    /**
     * Magic method to avoid getting undeclared properties.
     */
    public function __get($name)
    {
        throw new LogicException("Trying to access undeclared property '$name'.");
    }

    /**
     * Magic method to show only public properties when var_export and friends
     * are executed in this object.
     */
    public function __debugInfo()
    {
        return $this->asArray();
    }

    public function offsetExists($offset): bool
    {
        return in_array($this->properties, $offset);
    }

    public function offsetGet($offset): mixed
    {
        return $this->$offset;
    }

    public function offsetSet($offset, $value): void
    {
        $this->$offset = $value;
    }

    public function offsetUnset($offset): void
    {
        throw new LogicException("DataContainer object can't unset a property.");
    }

    public function current(): mixed
    {
        return $this->{current($this->properties)};
    }

    public function key(): mixed
    {
        return current($this->properties);
    }

    public function next(): void
    {
        next($this->properties);
    }

    public function rewind(): void
    {
        reset($this->properties);
    }

    public function valid(): bool
    {
        return current($this->properties) !== false;
    }

    public function asArray(): array
    {
        $props = [];
        foreach ($this->properties as $prop) {
            $props[$prop] = $this->$prop;
        }
        return $props;
    }
}