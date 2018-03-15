<?php

namespace XQueue\Maileon\API\Transactions;

use XQueue\Maileon\API\JSON\AbstractJSONWrapper;
use XQueue\Maileon\API\Transactions\ContactReference;
use XQueue\Maileon\API\Transactions\Attachment;
use XQueue\Maileon\API\Contacts\Permission;
use XQueue\Maileon\API\MaileonAPIException;

/**
 * The wrapper class for a Maileon transaction.
 * 
 * @author Viktor Balogh | Wanadis Kft. | <a href="balogh.viktor@maileon.hu">balogh.viktor@maileon.hu</a>
 * @author Marcus St&auml;nder | Trusted Technologies GmbH | <a href="mailto:marcus.staender@trusted-technologies.de">marcus.staender@trusted-technologies.de</a>
 */

class Transaction extends AbstractJSONWrapper {
    /**
     *
     * @var integer
     *      the numeric ID of the transaction type to use
     */
    public $type;

    /**
     *
     * @var com_maileon_api_transactions_ContactReference
     *      an array identifying the contact that contains at least one of the following attributes: id/external_id/email
     */
    public $contact;

    /**
     *
     * @var com_maileon_api_transactions_ContactReference
     *      Used to control import settings. This array also contains the contact. If $contact AND $import exist,
     *      $contact will be ignored and the contact within $import will be used.
     */
    public $import;

    /**
     *
     * @var array
     *      an array that contains the transaction content as defined in the referenced transaction type
     */
    public $content;

    /**
     * @var array
     *      an array that contains the attachments for the transaction e-mail, if any
     */
    public $attachments;
	
    /**
     * Constructor initializing default values.
     */
    function __construct() {
        $this->content = array();
        $this->attachments = array();
        $this->contact = new ContactReference();
        $this->import = new ContactReference();
        $this->import->permission = Permission::$NONE;
    }

    /**
     * Read a binary file from the file system and adds it as an attachment to this transaction.
     * @param $filename the file system path to read the attachment from
     * @param $mimetype the mime type of the attachment
     * @param null $attachmentFileName the file name to use when sending the attachment as an e-mail. If this is null,
     *        the basename of $filename is used instead.
     */
    function addAttachmentFromFile($filename, $mimetype, $attachmentFileName = null) {
        $handle = fopen($filename, "rb");
        if (FALSE === $filename) {
            throw new MaileonAPIException("Cannot read file " . $filename . ".");
        }
        $contents = '';
        while (!feof($handle)) {
            $contents .= fread($handle, 8192);
        }
        fclose($handle);
        if ($attachmentFileName === null) {
            $attachmentFileName = basename($filename);
        }
        $attachment = new Attachment($attachmentFileName, $mimetype, base64_encode($contents));
        $this->attachments[] = $attachment;
    }

    /**
     * Adds an attachment to this transaction from a string that contains binary data.
     * @param $filename the file name to use when sending the attachment as an e-mail
     * @param $mimetype the mime type of the attachment
     * @param $contents a string containing binary data, e.g. as returned from fread().
     */
    function addAttachmentFromBinaryData($filename, $mimetype, $contents) {
        $attachment = new Attachment($filename, $mimetype, base64_encode($contents));
        $this->attachments[] = $attachment;
    }

    /**
     * Adds an attachment to this transaction from a string that contains base64-encoded data.
     * @param $filename the file name to use when sending the attachment as an e-mail
     * @param $mimetype the mime type of the attachment
     * @param $contents a string containing binary data, e.g. as returned from fread().
     */
    function addAttachmentFromBase64Data($filename, $mimetype, $contents) {
        $attachment = new Attachment($filename, $mimetype, $contents);
        $this->attachments[] = $attachment;
    }
	
    /**
     * @return \em string 
     * 	a human-readable representation listing all the attributes of this transaction and their respective values.
     */
    function toString() {
            return "Transaction [type=" . $this->type .  ", contact=(" . (empty( $this->contact))?"":$this->contact . "), import=(" . (empty( $this->import))?"":$this->import . "), content=(" . json_encode($this->content) . ")]";
    }
    
    function toArray() {
        $array = parent::toArray();
        
        if(isset($array['import'])) {
            unset($array['contact']);
            $array['import'] = array( "contact" => $array['import'] );
        }
        
        return $array;
    }
}