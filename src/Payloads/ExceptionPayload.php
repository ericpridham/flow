<?php

namespace EricPridham\Flow\Payloads;

use EricPridham\Flow\Interfaces\FlowPayload;

class ExceptionPayload extends FlowPayload
{
    public $type = 'exception';

    public static function fromException(\Exception $exception)
    {
        return new static(null, [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
    }

    public function getTitle(): string
    {
        return 'Exception: ' . $this->data['message'];
    }

    public function getDetails()
    {
        return $this->data['file'] . ':' . $this->data['line'] . "\n\n" . $this->data['trace'];
    }
}
