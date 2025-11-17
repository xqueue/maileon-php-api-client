<?php

namespace de\xqueue\maileon\api\client\json;

use function array_key_exists;
use function array_map;
use function class_exists;
use function get_class;
use function get_object_vars;
use function implode;
use function is_array;
use function is_subclass_of;
use function mb_substr;

/**
 * Abstract base class for all JSON serializable elements.
 *
 * All classes derived from this must initialize their member classes in the
 * constructor.
 *
 * @author Viktor Balogh | XQueue GmbH | <a href="mailto:viktor.balog@xqueue.com">viktor.balog@xqueue.com</a>
 */
abstract class AbstractJSONWrapper
{
    public function __construct($params = [])
    {
        $object_vars = get_object_vars($this);

        foreach ($object_vars as $key => $value) {
            if (array_key_exists($key, $params)) {
                $this->{$key} = $params[$key];
            }
        }
    }

    protected function elementToArray($value)
    {
        // if $value is an object, we can call toArray on it, and it
        // should be serialized (isEmpty returns true) than we call
        // toArray and insert the result in our array;
        // otherwise we insert the value as-is
        if ($value instanceof self) {
            if ($value->isEmpty()) {
                return null;
            }

            return $value->toArray();
        }

        if (is_array($value)) {
            $result = array_map(
                function($element) {
                    return $this->elementToArray($element);
                },
                $value
            );

            if (empty($result)) {
                return null;
            }

            return $result;
        }

        // TODO: maybe deal with AbstractJSONWrapper
        // derived classes that have 'non-serializable' properties
        return $value;
    }

    /**
     * Used to serialize this object to a JSON string. Override this to modify
     * JSON parameters.
     *
     * @return array This class in array form
     */
    public function toArray(): array
    {
        $result      = [];
        $object_vars = get_object_vars($this);

        // copy each of this object's properties to an associative array
        // indexed by the property names
        foreach ($object_vars as $key => $value) {
            $converted = $this->elementToArray($value);

            if ($converted !== null) {
                $result[$key] = $converted;
            }
        }

        // return the resulting array
        return $result;
    }

    /**
     * Used to initialize this object from JSON. Override this to modify JSON
     * parameters.
     *
     * @param array $object_vars The array from json_decode
     */
    public function fromArray($object_vars)
    {
        // copy each key to the property named the same way; if the property
        // is a serializable Maileon class, call fromArray on it
        foreach ($object_vars as $key => $value) {
            if (class_exists(__CLASS__)
                && is_subclass_of($this->{$key}, __CLASS__)) {
                $this->{$key}->fromArray($value);
            } else {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Creates a string representation from this object in the following format:
     * ObjectName [ property1=value1, property2=value2, ... , propertyN=valueN ]
     *
     * @return string
     */
    public function __toString()
    {
        $object_vars = get_object_vars($this);
        $elements    = '';

        // add each property of this class to a string
        foreach ($object_vars as $key => $value) {
            $flat     = is_array($value) ? '[' . implode(',', $value) . ']' : $value;
            $elements .= $key . '=' . $flat . ', ';
        }

        return get_class($this) . ' [ ' . mb_substr($elements, 0, -2) . ' ]';
    }

    /**
     * Human-readable representation of this object
     *
     * @return string A human-readable representation of this object
     */
    public function toString(): string
    {
        return $this->__toString();
    }

    /**
     * Can be overridden in derived classes to signal that this object is empty
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return false;
    }
}
