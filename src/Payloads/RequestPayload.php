<?php

namespace EricPridham\Flow\Payloads;

use EricPridham\Flow\Interfaces\FlowPayload;
use Illuminate\Foundation\Http\Events\RequestHandled;

class RequestPayload extends FlowPayload
{
    public $type = 'request';
    public $color = '#955EDA';

    public static function fromRequestHandled(RequestHandled $event)
    {
        return new static(null, [
            'url' => $event->request->fullUrl(),
            'method' => $event->request->method(),
            'contents' => $event->request->all(),
            'response' => json_decode($event->response->getContent(), /*$assoc=*/ true),
        ]);
    }

    public function getTitle(): string
    {
        return 'HTTP Request: ' . $this->data['method'] . ' ' . $this->data['url'];
    }

    public function getDetails()
    {
        $details = [
            'contents' => $this->data['contents'],
        ];
        if ($this->data['response']) {
            $details['response'] = $this->data['response'];
        }

        return $details;
    }
}
