<?php
namespace de\xqueue\maileon\api\client\blacklists\mailings;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * The wrapper class for an expression of a Maileon mailing blacklist (Versandsperrliste in German). This class wraps the XML structure.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class MailingBlacklistExpressions extends AbstractXMLWrapper
{
    /**
     * The list of expressions
     * @var string[]
     */
    public $expressions;

    /**
     * Constructor initializing default values.
     *
     * @param string[] $expressions
     */
    public function __construct(
        $expressions = array()
    ) {
        $this->expressions = $expressions;
    }

    /**
     * Initialization of the mailing blacklist expression(s) from a simple xml element.
     *
     * @param \SimpleXMLElement $xmlElement
     *  The xml element that is used to parse the attachment from.
     */
    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->expressions)) {
            $this->expressions = array();
            foreach ($xmlElement->expressions->children() as $entry) {
                $this->expressions[] = $entry;
            }
        }
    }

    /**
     * Creates the XML representation of the mailing blacklist expressions
     *
     * @return \SimpleXMLElement
     */
    public function toXML()
    {
        $xmlString = "<?xml version=\"1.0\"?><mailing_blacklist_expressions></mailing_blacklist_expressions>";
        $xml = new \SimpleXMLElement($xmlString);

        if (isset($this->expressions)) {
            $expressions = $xml->addChild("expressions");
            foreach ($this->expressions as $expression) {
                $expressions->addChild("expression", $expression);
            }
        }
        return $xml;
    }

    /**
     * Human readable representation of this wrapper.
     *
     * @return string
     *  A human readable version of the mailing.
     */
    public function toString()
    {
        return "MailingBlacklist [expressions=[" .
            (is_array($this->expressions) ? implode(", ", $this->expressions) : "") . "]]";
    }
}