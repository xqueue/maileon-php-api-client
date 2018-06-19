<?php

namespace XQueue\Maileon\API\JSON;

use XQueue\Maileon\API\JSON\AbstractJSONWrapper;
use XQueue\Maileon\API\JSON\JSONSerializer;

/**
 * Helper class for serializing JSON data
 *
 * @author Balogh Viktor <balogh.viktor@maileon.hu> | Maileon - Wanadis Kft.
 */
class JSONSerializer {
    /**
     * Used to serialize Maileon classes to JSON
     * 
     * @param AbstractJSONWrapper|array $object
     *  The object to serialize
     * @returns string
     *  The JSON representation of the object
     */
    static function json_encode($object) {
        return json_encode(self::toArray($object));
    }
    
    /**
     * Helper function used to prepare this object for JSON serialization
     * 
     * @param array|com_maileon_api_json_AbstractJSONWrapper $object
     *  The object to prepare
     * @return array
     *  The array ready for json_encode
     */
    private static function toArray($object) {     
        $type = gettype($object);
        
        if($type == 'array') {            
            foreach($object as $element) {
                // call this method on each object in the array
                $result[]= self::toArray($element);
            }
            
            // return the processed array
            return $result;
        } else if ($type == 'object') {  
            // if we can call toArray() on this object call it, otherwise return
            // the object as-is and trigger a notice
            if(is_subclass_of($object, 'XQueue\Maileon\API\JSON\AbstractJSONWrapper')) { 
                return $object->toArray();
            } else {
                trigger_error("JSONSerializer: Trying to serialize " . get_class($object)); 
                return $object;
            }
        } else {
            // if this is not an object we have nothing to do
            return $object;
        }
    }
}