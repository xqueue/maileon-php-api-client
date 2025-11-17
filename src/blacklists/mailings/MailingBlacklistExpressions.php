<?php

namespace de\xqueue\maileon\api\client\blacklists\mailings;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;
use SimpleXMLElement;

use function implode;
use function is_array;

/**
 * The wrapper class for an expression of a Maileon mailing blacklist (Versandsperrliste in German). This class wraps the XML structure.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class MailingBlacklistExpressions extends AbstractXMLWrapper
{
    /**
     * The list of expressions
     *
     * @var string[]
     */
    public $expressions;

    /**
     * Constructor initializing default values.
     *
     * @param string[] $expressions
     */
    public function __construct($expressions = [])
    {
        $this->expressions = $expressions;
    }

    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->expressions)) {
            $this->expressions = [];

            foreach ($xmlElement->expressions->children() as $entry) {
                $this->expressions[] = $entry;
            }
        }
    }

    public function toXML()
    {
        $xmlString = '<?xml version="1.0"?><mailing_blacklist_expressions></mailing_blacklist_expressions>';
        $xml       = new SimpleXMLElement($xmlString);

        if (isset($this->expressions)) {
            $expressions = $xml->addChild('expressions');

            foreach ($this->expressions as $expression) {
                $expressions->addChild('expression', $expression);
            }
        }

        return $xml;
    }

    public function toString(): string
    {
        return 'MailingBlacklist ['
            . 'expressions=[' . (is_array($this->expressions) ? implode(', ', $this->expressions) : '') . ']'
            . ']';
    }
}