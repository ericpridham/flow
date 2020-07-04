<?php

namespace EricPridham\Flow\Recorder;

use EricPridham\Flow\Interfaces\FlowPayload;
use EricPridham\Flow\Models\FlowEvents;
use Illuminate\Database\Eloquent\Builder;

class DatabaseRecorder implements FlowRecorder
{
    public function init()
    {
    }

    public function record(string $requestId, FlowPayload $payload)
    {
        $event = new FlowEvents();
        $event->request_id = $requestId;
        $event->payload = $payload;
        $event->save();
    }

    public function retrieve(): Builder
    {
        return FlowEvents::orderByDesc('id');
    }
}
