<?php

namespace EricPridham\Flow\Payloads;

use Carbon\Carbon;
use EricPridham\Flow\Interfaces\FlowPayload;
use Illuminate\Foundation\Http\Events\RequestHandled;
use OpenTracing\Tracer;

class RequestPayload extends FlowPayload
{
    public string $type = 'request';
    public string $color = '#955EDA';

    public static function fromRequestHandled(RequestHandled $event): static
    {
        return new static(null, [
            'url' => $event->request->fullUrl(),
            'method' => $event->request->method(),
            'contents' => $event->request->all(),
            'response' => json_decode($event->response->getContent(), /*$assoc=*/ true),
            'status_code' => $event->response->getStatusCode(),
            'error' => !$event->response->isSuccessful()
        ]);
    }

    public function getTitle(): string
    {
        return 'HTTP Request: ' . $this->data['method'] . ' ' . $this->data['url'];
    }

    public function getDetails(): mixed
    {
        $details = [
            'contents' => $this->data['contents'],
        ];
        if ($this->data['response']) {
            $details['response'] = $this->data['response'];
        }

        return $details;
    }

    public function addTraceData(Tracer $tracer, ?Carbon $start): void
    {
        $span = $tracer->getActiveSpan();
        $span->setTag('error', $this->data['error']);
        $span->setTag('http.url', $this->data['url']);
        $span->setTag('http.method', $this->data['method']);
        $span->setTag('http.status_code', $this->data['status_code']);
    }
}
