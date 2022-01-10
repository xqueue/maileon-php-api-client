<?php

namespace de\xqueue\maileon\api\client\mailings;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

/**
 * A wrapper class for an image grabbing result
 *
 * @author Balogh Viktor <balogh.viktor@maileon.hu> | Maileon - Wanadis Kft.
 */
class ImageGrabbingResult extends AbstractJSONWrapper
{
    /**
     * The modified content
     *
     * @var ReportContact
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
