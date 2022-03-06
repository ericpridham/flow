<?php

namespace EricPridham\Flow\Payloads;

use Carbon\Carbon;
use EricPridham\Flow\Interfaces\FlowPayload;
use OpenTracing\Tracer;

class ExceptionPayload extends FlowPayload
{
    public string $type = 'exception';
    public string $color = "#F93822";

    public static function fromException(\Throwable $exception): static
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

    public function getDetails(): mixed
    {
        return $this->data['trace'];
    }

    public function addTraceData(Tracer $tracer, ?Carbon $start): void
    {
        $span = $tracer->getActiveSpan();
        $span->setTag('error', true);

        $span->setTag('error.message', $this->data['message']);
        $span->setTag('error.type', $this->data['class']);
        $span->setTag('error.file', $this->data['file']);
        $span->setTag('error.line', $this->data['line']);
    }
}
