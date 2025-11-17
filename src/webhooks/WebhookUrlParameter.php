<?php

namespace de\xqueue\maileon\api\client\webhooks;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

use function property_exists;
use function strtolower;

/**
 * A wrapper class for a webhook URL parameter
 *
 * @author Viktor Balogh | XQueue GmbH | <a href="mailto:viktor.balog@xqueue.com">viktor.balog@xqueue.com</a>
 */
class WebhookUrlParameter extends AbstractJSONWrapper
{
    /**
     * The custom contact field for this parameter
     *
     * @var string|null
     */
    public $customContactField;

    /**
     * The standard contact field for this parameter
     *
     * @var string|null
     */
    public $standardContactField;

    /**
     * The custom value for this parameter
     *
     * @var string|null
     */
    public $customValue;

    /**
     * The name for this parameter
     *
     * @var string
     */
    public $name;

    public function toArray(): array
    {
        $result = [];

        if ($this->name !== null) {
            $result['name'] = $this->name;
        }

        if ($this->standardContactField !== null) {
            $result['standard_contact_field'] = strtolower($this->standardContactField);
        }

        if ($this->standardContactField !== null) {
            $result['custom_contact_field'] = $this->customContactField;
        }

        if ($this->customValue !== null) {
            $result['custom_value'] = $this->customValue;
        }

        return $result;
    }

    public function fromArray($object_vars)
    {
        if (property_exists($object_vars, 'standard_contact_field')) {
            $this->standardContactField = $object_vars->standard_contact_field;
            unset($object_vars->standard_contact_field);
        }

        if (property_exists($object_vars, 'custom_contact_field')) {
            $this->customContactField = $object_vars->custom_contact_field;
            unset($object_vars->custom_contact_field);
        }

        if (property_exists($object_vars, 'custom_value')) {
            $this->customValue = $object_vars->custom_value;
            unset($object_vars->custom_value);
        }

        parent::fromArray($object_vars);
    }
}
