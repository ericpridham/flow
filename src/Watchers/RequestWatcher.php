<?php

namespace EricPridham\Flow\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Interfaces\FlowWatcher;
use EricPridham\Flow\Payloads\RequestPayload;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Event;

class RequestWatcher implements FlowWatcher
{
    public function register(Flow $flow, array $params): void
    {
        Event::listen(RequestHandled::class, function (RequestHandled $event) use ($flow) {
            if ($event->request->is(config('flow.path') . '*')) {
                return;
            }
            $flow->record(RequestPayload::fromRequestHandled($event));
        });
    }
}
