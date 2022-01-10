<?php

namespace de\xqueue\maileon\api\client\webhooks;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

/**
 * A wrapper class for a webhook URL parameter
 *
 * @author Balogh Viktor <balogh.viktor@maileon.hu> | Maileon - Wanadis Kft.
 */
class WebhookUrlParameter extends AbstractJSONWrapper
{
    /**
     * The custom contact field for this parameter
     *
     * @var string|null
     */
    public $customContactField = null;

    /**
     * The standard contact field for this parameter
     *
     * @var string|null
     */
    public $standardContactField = null;

    /**
     * The custom value for this parameter
     *
     * @var string|null
     */
    public $customValue = null;

    /**
     * The name for this parameter
     *
     * @var string
     */
    public $name = null;

    public function toArray()
    {
        $result = [];

        if($this->name !== null) {
            $result['name'] = $this->name;
        }

        if($this->standardContactField !== null) {
            $result['standard_contact_field'] = strtolower($this->standardContactField);
        }

        if($this->standardContactField !== null) {
            $result['custom_contact_field'] = $this->customContactField;
        }

        if($this->customValue !== null) {
            $result['custom_value'] = $this->customValue;
        }

        return $result;
    }

    public function fromArray($object_vars)
    {
        if(property_exists($object_vars, 'standard_contact_field')) {
            $this->standardContactField = $object_vars->standard_contact_field;
            unset($object_vars->standard_contact_field);
        }

        if(property_exists($object_vars, 'custom_contact_field')) {
            $this->customContactField = $object_vars->custom_contact_field;
            unset($object_vars->custom_contact_field);
        }

        if(property_exists($object_vars, 'custom_value')) {
            $this->customValue = $object_vars->custom_value;
            unset($object_vars->custom_value);
        }

        parent::fromArray($object_vars);
    }
}
