<?php

namespace de\xqueue\maileon\api\client\xml;

/**
 * Utility class for XML elements
 *
 * @author Felix Heinrichs | Trusted Mails GmbH |
 * <a href="mailto:felix.heinrichs@trusted-mails.com">felix.heinrichs@trusted-mails.com</a>
 * @author Marcus Beckerle | XQueue GmbH |
 * <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
abstract class XMLUtils
{

    /**
     * This function appends one SimpleXMLElement to another one as addChild does not support deep copies
     * @param \SimpleXMLElement $to
     * @param \SimpleXMLElement $from
     */
    public static function appendChild(\SimpleXMLElement $to, \SimpleXMLElement $from)
    {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }

    /**
     * Adds a child with $value inside CDATA
     * @param \SimpleXMLElement $parent
     * @param string $name
     * @param string $value
     */
    public static function addChildAsCDATA($parent, $name, $value = null)
    {
        $new_child = $parent->addChild($name);

        if ($new_child !== null) {
            $node = dom_import_simplexml($new_child);
            $no = $node->ownerDocument;
            // createCDATASection() returns an empty CDATA if value is a false boolean
            // workaround: use intval of value instead
            $cdata = $no->createCDATASection((string)(is_bool($value) ? intval($value) : $value));
            $node->appendChild($cdata);
        }

        return $new_child;
    }
}
