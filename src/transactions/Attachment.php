<?php

namespace de\xqueue\maileon\api\client\transactions;

/**
 * A binary file attached to a transaction e-mail.
 *
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class Attachment
{
    public $filename;
    public $mimetype;
    public $data;

    /**
     * Creates a new attachment. Please use the factory methods
     * in Transaction instead of calling this constructor directly.
     * @param $filename
     * @param $mimetype
     * @param $data
     */
    public function __construct($filename, $mimetype, $data)
    {
        $this->filename = $filename;
        $this->mimetype = $mimetype;
        $this->data = $data;
    }

    public function toString()
    {
        return "Attachment [filename=" . $this->filename .
            ", mimetype=(" . $this->mimetype . "), data=(" . $this->data . ")]";
    }
}
