<?php

namespace EricPridham\Flow\Payloads;

use EricPridham\Flow\Interfaces\FlowPayload;

class ExceptionPayload extends FlowPayload
{
    public $type = 'exception';
    public $color = "#F93822";

    public static function fromException(\Throwable $exception)
    {
        return new static(null, [
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
    }

    public function getTitle(): string
    {
        return ($this->data['class']??'Exception')
            . ': '
            . $this->data['message']
            . ' <span style="opacity: 0.7;">' . $this->data['file'] . '@' . $this->data['line'] . '</span>';
    }

    public function getDetails()
    {
        return $this->data['trace'];
    }
}
