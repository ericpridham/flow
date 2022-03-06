<?php


namespace EricPridham\Flow\Payloads;


use Carbon\Carbon;
use EricPridham\Flow\Interfaces\FlowPayload;
use Illuminate\Log\Events\MessageLogged;
use OpenTracing\Tracer;

class LogPayload extends FlowPayload
{
    public string $type = 'log';
    public string $color = '#999';

    public static function fromMessageLogged(MessageLogged $event): static
    {
        return new static(null, [
            'level' => $event->level,
            'message' => $event->message,
            'context' => $event->context,
        ]);
    }

    public function getTitle(): string
    {
        return ucfirst($this->data['level']) . ': ' . $this->data['message'];
    }

    public function getDetails(): mixed
    {
        return $this->data['context'];
    }

    public function addTraceData(Tracer $tracer, ?Carbon $start): void
    {
        $span = $tracer->getActiveSpan();
        $span->log([json_encode([$this->type => $this->data])], $start?->getPreciseTimestamp());
    }
}
