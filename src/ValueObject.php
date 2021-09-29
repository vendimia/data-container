<?php
namespace Vendimia\DataContainer;

use ReflectionClass;
use ReflectionProperty;
use ArrayAccess;
use Iterator;
use LogicException;
use Traversable;
use IteratorAggregate;
use ArrayIterator;
use Vendimia\Interface\DataType\Arrayable;

/**
 * Value object helper class.
 *
 * A class extending a ValueObject class should define its properties as
 *  _public readonly_ (on PHP8.1+) or _protected_ (on PHP8.0) on the constructor.
 *
 * ValueObject objects are immutable. Private or public properties can be accessed
 * like public properties or array indexes. Readonlyness in PHP8.0 is achieved
 * by implementing a magic __set() method.
 */
abstract class ValueObject implements ArrayAccess, IteratorAggregate, Arrayable
{
    /**
     * Magic method to avoid setting undeclared properties.
     */
    public function __set($name, $value)
    {
        throw new LogicException("This value object is immutable, trying to set value to property '{$name}'.");
    }

    /**
     * Magic method to avoid getting undeclared properties.
     */
    public function __get($name)
    {
        // For PHP8.0, private properties are accessed as public
        if (isset($this->$name)) {
            return $this->name;
        }
        throw new LogicException("Trying to access undeclared property '{$name}'.");
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
        throw new LogicException("This value object is immutable, trying to set value to property '{$name}'.");
    }

    public function offsetUnset($offset): void
    {
        throw new LogicException("This value object is immutable, trying to unset property '{$offset}'.");
    }

    /**
     * IteratorAggregate implementation
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->asArray());
    }


    /**
     * Vendimia\Interface\DataType\Arrayable implementation
     */
    public function asArray(): array
    {
        $return = [];
        $rc = new ReflectionClass($this);
        foreach($rc->getProperties() as $rp) {
            $return[$rp->getName()] = $rp->getValue($this);
        }

        return $return;
    }
}