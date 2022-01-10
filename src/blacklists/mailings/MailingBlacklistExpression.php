<?php

namespace de\xqueue\maileon\api\client\blacklists\mailings;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;

/**
 * A wrapper class for a list of mailing blacklist expressions. This class wraps a signle JSON structure.
 *
 * @author Balogh Viktor <balogh.viktor@maileon.hu> | Maileon - Wanadis Kft.
 */
class MailingBlacklistExpression extends AbstractJSONWrapper
{
    /**
     * The name of this expression
     *
     * @var string
     */
    public $name = '';

    /**
     * The category of this expression
     *
     * @var string
     */
    public $category = '';
}
