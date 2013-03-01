<?php
class XmlOperationException extends RuntimeException
{
    private $type = XmlErrorType::NoError;
    public function __construct($message, $type, Exception $previous = null) {
        $this->type = $type;
        parent::__construct($message, 0, $previous);
    }
    public function getType() {return $this->type;}
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}