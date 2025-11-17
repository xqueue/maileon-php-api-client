<?php

namespace de\xqueue\maileon\api\client\mailings;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

use function htmlentities;
use function property_exists;

/**
 * A wrapper class for an image grabbing result
 *
 * @author Viktor Balogh | XQueue GmbH | <a href="mailto:viktor.balog@xqueue.com">viktor.balog@xqueue.com</a>
 */
class ImageGrabbingResult extends AbstractJSONWrapper
{
    /**
     * The modified content
     */
    public $modified_content = '';

    /**
     * The error messages (if there was any)
     *
     * @var string[]
     */
    public $errors = [];

    public function fromArray($object_vars)
    {
        if (property_exists($object_vars, 'errors')) {
            $this->errors = $object_vars->errors;
        }

        $this->modified_content = $object_vars->modified_content;
    }

    public function __toString()
    {
        return htmlentities(parent::__toString());
    }
}
