<?php

namespace Maileon\Json;

/**
 * Abstract base class for all JSON serializable elements.
 *
 * All classes derived from this must initialize their member classes in the
 * constructor.
 *
 * @author Balogh Viktor <balogh.viktor@maileon.hu> | Maileon - Wanadis Kft.
 */
abstract class AbstractJSONWrapper
{
    /**
     * Used to serialize this object to a JSON string. Override this to modify
     * JSON parameters.
     *
     * @return array
     *  This class in array form
     */
    public function toArray()
    {
        $result = array();
        $object_vars = get_object_vars($this);
       
        // copy each of this objects properties to an associative array
        // indexed by the property names
        foreach ($object_vars as $key => $value) {
            if ($value == null) {
                continue;
            }
            
            // if $value is an object, we can call toArray on it, and it
            // should be serialized (isEmpty returns true) than we call
            // toArray and insert the result in our array;
            // otherwise we insert the value as-is
            if (gettype($value) == "object" && is_subclass_of($value, 'Maileon\Json\AbstractJSONWrapper')) {
                if (!$value->isEmpty()) {
                    $result[$key] = $value->toArray();
                }
            } else {
                // TODO: maybe deal with AbstractJSONWrapper
                // derived classes that have 'non-serializable' properties
                $result[$key] = $value;
            }
        }
        
        // return the resulting array
        return $result;
    }
    
    /**
     * Used to initialize this object from JSON. Override this to modify JSON
     * parameters.
     *
     * @param array $object_vars
     *  The array from json_decode
     */
    public function fromArray($object_vars)
    {
        // copy each key to the property named the same way; if the property
        // is a serializable Maileon class, call fromArray on it
        foreach ($object_vars as $key => $value) {
            if (class_exists('Maileon\Json\AbstractJSONWrapper') &&
            is_subclass_of($this->{$key}, 'Maileon\Json\AbstractJSONWrapper' )) {
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
        $elements = "";
        
        // add each property of this class to a string
        foreach ($object_vars as $key => $value) {
            $elements .= $key . "=" . $value . ", ";
        }
        
        return get_class($this) . " [ " . mb_substr($elements, 0, mb_strlen($elements) - 2) . " ]";
    }
    
    /**
     * Can be overridden in derived classes to signal that this object is empty
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return false;
    }
}
