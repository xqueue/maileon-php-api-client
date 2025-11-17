<?php

namespace de\xqueue\maileon\api\client\json;

use function class_exists;
use function count;
use function get_class;
use function is_array;
use function json_decode;
use function trigger_error;

/**
 * Helper class for deserializing JSON data
 *
 * @author Viktor Balogh | XQueue GmbH | <a href="mailto:viktor.balog@xqueue.com">viktor.balog@xqueue.com</a>
 */
class JSONDeserializer
{
    /**
     * Used to deserialize a Maileon class from JSON
     *
     * @param string $jsonString          The JSON string to deserialize
     * @param mixed  $deserializationType The type of the deserialized object. For deserializing arrays use array( 'array', 'typename' )
     *
     * @return AbstractJSONWrapper|array
     */
    public static function json_decode(
        $jsonString,
        $deserializationType = null
    ) {
        if (is_array($deserializationType) && count($deserializationType) > 1) {
            $type      = $deserializationType[0];
            $innerType = $deserializationType[1];
        } else {
            $type      = $deserializationType;
            $innerType = null;
        }

        // return self::fromArray(json_decode($jsonString, true), $type, $innerType);
        return self::fromArray(json_decode($jsonString), $type, $innerType);
    }

    /**
     * Helper function used to build this class from an array.
     *
     * @param array  $object    The result of json_decode
     * @param string $type      The name of the type
     * @param string $innerType The name of the inner type
     *
     * @return array|AbstractJSONWrapper  The parsed object
     */
    private static function fromArray(
        $object,
        $type = null,
        $innerType = null
    ) {
        if ($type === 'array') {
            $result = [];

            foreach ($object as $element) {
                // call this method on each element
                $result[] = self::fromArray($element, $innerType);
            }

            // return the processed array
            return $result;
        }

        if ($type !== null && class_exists($type)) {
            // create the class we are deserializing
            $class = new $type();

            // if we can call fromArray on the class call it, otherwise
            // return the object as-is and trigger a warning
            if ($class instanceof AbstractJSONWrapper) {
                $class->fromArray($object);

                return $class;
            }

            trigger_error(__CLASS__ . ': Trying to deserialize ' . get_class($class));

            return $object;
        }

        // if this is not a class, we have nothing to do
        return $object;
    }
}
