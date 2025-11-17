<?php

namespace de\xqueue\maileon\api\client\transactions;

use de\xqueue\maileon\api\client\json\AbstractJSONWrapper;
use de\xqueue\maileon\api\client\MaileonAPIException;

use function base64_encode;
use function basename;
use function fclose;
use function feof;
use function fopen;
use function fread;
use function json_encode;

/**
 * The wrapper class for a Maileon transaction.
 *
 * @author Viktor Balogh | XQueue GmbH | <a href="mailto:viktor.balog@xqueue.com">viktor.balog@xqueue.com</a>
 * @author Marcus Beckerle | XQueue GmbH | <a href="mailto:marcus.beckerle@xqueue.com">marcus.beckerle@xqueue.com</a>
 */
class Transaction extends AbstractJSONWrapper
{
    /**
     * the numeric ID of the transaction type to use
     *
     * @var int
     */
    public $type;

    /**
     * the name of the transaction type to use as an alternative to using the ID
     *
     * @var string
     */
    public $typeName;

    /**
     * an array identifying the contact that contains at least one of the following attributes: id/external_id/email
     *
     * @var ContactReference
     */
    public $contact;

    /**
     * Used to control import settings. This array also contains the contact. If $contact AND $import exist,
     * $contact will be ignored and the contact within $import will be used.
     *
     * @var ContactReference
     */
    public $import;

    /**
     * an array that contains the transaction content as defined in the referenced transaction type
     *
     * @var array
     */
    public $content;

    /**
     * an array that contains the attachments for the transaction e-mail, if any
     *
     * @var array
     */
    public $attachments;

    /**
     * Constructor initializing default values.
     */
    public function __construct()
    {
        // parent::__construct() ?

        $this->content     = [];
        $this->attachments = [];
        $this->contact     = new ContactReference();

        // $this->import                      = new \de\xqueue\maileon\api\client\transactions\ImportReference();
        // $this->import->contact             = new \de\xqueue\maileon\api\client\transactions\ImportContactReference();
        // $this->import->contact->permission = \de\xqueue\maileon\api\client\contacts\Permission::$NONE;
    }

    /**
     * Read a binary file from the file system and adds it as an attachment to this transaction.
     *
     * @param string $filename           the file system path to read the attachment from
     * @param string $mimetype           the mime type of the attachment
     * @param        $attachmentFileName the file name to use when sending the attachment as an e-mail. If this is null, the basename of
     *                                   $filename is used instead.
     */
    public function addAttachmentFromFile(
        $filename,
        $mimetype,
        $attachmentFileName = null
    ) {
        $handle = fopen($filename, "rb");

        if (false === $handle) {
            throw new MaileonAPIException("Cannot read file $filename.");
        }

        $contents = '';

        while (! feof($handle)) {
            $contents .= fread($handle, 8192);
        }
        fclose($handle);

        if ($attachmentFileName === null) {
            $attachmentFileName = basename($filename);
        }

        $attachment          = new Attachment($attachmentFileName, $mimetype, base64_encode($contents));
        $this->attachments[] = $attachment;
    }

    /**
     * Adds an attachment to this transaction from a string that contains binary data.
     *
     * @param $filename string the file name to use when sending the attachment as an e-mail
     * @param $mimetype string the mime type of the attachment
     * @param $contents string a string containing binary data, e.g. as returned from fread().
     */
    public function addAttachmentFromBinaryData(
        $filename,
        $mimetype,
        $contents
    ) {
        $attachment          = new Attachment($filename, $mimetype, base64_encode($contents));
        $this->attachments[] = $attachment;
    }

    /**
     * Adds an attachment to this transaction from a string that contains base64-encoded data.
     *
     * @param $filename string the file name to use when sending the attachment as an e-mail
     * @param $mimetype string the mime type of the attachment
     * @param $contents string a string containing binary data, e.g. as returned from fread().
     */
    public function addAttachmentFromBase64Data(
        $filename,
        $mimetype,
        $contents
    ) {
        $attachment          = new Attachment($filename, $mimetype, $contents);
        $this->attachments[] = $attachment;
    }

    public function toString(): string
    {
        return 'Transaction ['
            . 'type=' . $this->type
            . ', contact=(' . ((empty($this->contact)) ? '' : $this->contact->toString()) . ')'
            . ', import=(' . ((empty($this->import)) ? '' : $this->import->toString()) . ')'
            . ', content=(' . json_encode($this->content) . ')'
            . ']';
    }

    public function toArray(): array
    {
        $array = parent::toArray();

        if (isset($array['import'])) {
            unset($array['contact']);
            // $array['import'] = array( 'contact' => $array['import'] );
        }

        return $array;
    }
}
