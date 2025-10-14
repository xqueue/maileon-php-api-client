<?php

namespace de\xqueue\maileon\api\client\xml;

class Error extends AbstractXMLWrapper {
  private $code;

  private $message;

  public function fromXML($xmlElement) {
    if (isset($xmlElement->code)) {
      $this->code = (string) $xmlElement->code;
    }
    if (isset($xmlElement->message)) {
      $this->message = (string) $xmlElement->message;
    }
  }

  public function getCode() {
    return $this->code;
  }

  public function getMessage() {
    return $this->message;
  }

  public function toString() {
    return 'Error[code='.$this->code.', message='.$this->message.']';
  }

  public function toXML() {
    $xmlString = '<error>';
    if (null !== $this->code) {
      $xmlString .= '<code>'.htmlspecialchars($this->code).'</code>';
    }
    if (null !== $this->message) {
      $xmlString .= '<message>'.htmlspecialchars($this->message).'</message>';
    }
    $xmlString .= '</error>';

    return new \SimpleXMLElement($xmlString);
  }
}
