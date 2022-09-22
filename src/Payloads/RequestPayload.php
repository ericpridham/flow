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
        // If the response exceeds the length of the TEXT field, it will get truncated. Respond with message instead of error.
        // 65535 is the bytelength of TEXT columns, but we are saving some additional data around this so truncate if > 65000
        $decodedResponse = (mb_strlen($event->response->getContent()) > 65000)
            ? "Response Too Large"
            : json_decode($event->response->getContent(), true);
        return new static(null, [
            'url' => $event->request->fullUrl(),
            'method' => $event->request->method(),
            'contents' => $event->request->all(),
            'response' => $decodedResponse,
            'status_code' => $event->response->getStatusCode(),
            'error' => !$event->response->isSuccessful()
        ]);
    }

    public function getTitle(): string
    {
        if (empty($this->data)) return "HTTP Request: Malformed JSON Response";
        return 'HTTP Request: ' . $this->data['method'] . ' ' . $this->data['url'];
    }

    public function getDetails(): mixed
    {
        if (empty($this->data)) return [];
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
        if ($this->data['error']) {
            $span->setTag('error', true);
        }
        $span->setTag('http.url', $this->data['url']);
        $span->setTag('http.method', $this->data['method']);
        $span->setTag('http.status_code', $this->data['status_code']);
    }
}
