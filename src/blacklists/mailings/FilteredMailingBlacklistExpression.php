<?php
namespace de\xqueue\maileon\api\client\blacklists\mailings;

use de\xqueue\maileon\api\client\xml\AbstractXMLWrapper;

/**
 * The wrapper class for a expression of a Maileon mailing blacklist (Versandsperrliste in German) that has been filtered/not been accepted by Maileon API. This class wraps the XML structure.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class FilteredMailingBlacklistExpression extends AbstractXMLWrapper
{
    /**
     * The expressions
     * @var string
     */
    public $expression;
    
    /**
     * The reason why it was not accepted
     * @var string
     */
    public $reason;

    /**
     * Constructor initializing default values.
     *
     * @param string $expression
     * @param string $reason
     */
    public function __construct(
        $expression = null,
        $reason = null
        ) {
            $this->expression = $expression;
            $this->reason = $reason;
    }

    /**
     * Initialization of the filtered mailing blacklist expression from a simple xml element.
     *
     * @param \SimpleXMLElement $xmlElement
     *  The xml element that is used to parse the attachment from.
     */
    public function fromXML($xmlElement)
    {
        if (isset($xmlElement->expression)) {
            $this->expression = $xmlElement->expression;
        }
        if (isset($xmlElement->reason)) {
            $this->reason = $xmlElement->reason;
        }
    }

    /**
     * Creates the XML representation of the mailing blacklist expressions
     *
     * @return \SimpleXMLElement
     */
    public function toXML()
    {
        $xmlString = "<?xml version=\"1.0\"?><filtered_expression></filtered_expression>";
        $xml = new \SimpleXMLElement($xmlString);
        
        if (isset($this->expression)) {
            $xml->addChild("expression", $this->expression);
        }
        if (isset($this->reason)) {
            $xml->addChild("reason", $this->reason);
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
        return "FilteredExpression [expression=" . $this->expression . ", reason=" . $this->reason . "]";
    }
}
