<?php

namespace de\xqueue\maileon\api\client\contactfilters;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

/**
 * The JSON wrapper class for a single rule of a v2 (JSON) contact filter.
 *
 * @see https://support.maileon.com/support/contactfilter-schema/
 */
class ContactFilterRuleV2 extends AbstractJSONWrapper
{
    /**
     * Only required/used on the first rule of a filter: 'all' | 'active' | 'empty'
     *
     * @var string|null
     */
    public $startset;

    /**
     * 'add' | 'remove' | 'intersection' | 'deduplicate' | 'top_n' | 'sample'
     *
     * @var string
     */
    public $operation;

    /**
     * The condition body. Its shape depends on the selection_base and operation, see Maileon docs.
     *
     * @var array
     */
    public $selection = [];
}
