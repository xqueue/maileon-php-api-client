<?php

namespace de\xqueue\maileon\api\client\contactfilters;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

/**
 * The JSON wrapper class for a v2 contact filter, as expected by the contactfilters/v2 endpoint.
 *
 * @see https://support.maileon.com/support/contactfilter-schema/
 */
class ContactFilterV2 extends AbstractJSONWrapper
{
    public $name = '';

    /**
     * @var ContactFilterRuleV2[]
     */
    public $rules = [];

    /**
     * Adds a rule to the contact filter.
     *
     * @param ContactFilterRuleV2 $rule
     */
    public function addRule($rule)
    {
        $this->rules[] = $rule;
    }
}
