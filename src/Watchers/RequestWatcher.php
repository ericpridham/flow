<?php

namespace EricPridham\Flow\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\FlowHelpers;
use EricPridham\Flow\Interfaces\FlowWatcher;
use EricPridham\Flow\Payloads\RequestPayload;
use EricPridham\Flow\Recorder\DatabaseRecorder;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Event;

class RequestWatcher implements FlowWatcher
{
    public function register(Flow $flow, array $params): void
    {
        Event::listen(RequestHandled::class, function (RequestHandled $event) use ($flow) {
            // hack for now. never record the flow path
            $flowPath = config('flow.recorders')[DatabaseRecorder::class]['path']??'flow';
            if ($event->request->is($flowPath . '*')) {
                return;
            }

            $start = FlowHelpers::getAppStart() ?? $event->request->server('REQUEST_TIME_FLOAT');
            $durationMs = FlowHelpers::calcDurationMs($start);

            $flow->record(RequestPayload::fromRequestHandled($event), $start, $durationMs);
        });
    }
}
