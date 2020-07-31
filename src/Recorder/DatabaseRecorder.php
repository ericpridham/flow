<?php

namespace EricPridham\Flow\Recorder;

use Carbon\Carbon;
use EricPridham\Flow\Interfaces\FlowPayload;
use EricPridham\Flow\Models\FlowEvents;
use Illuminate\Database\Eloquent\Builder;

class DatabaseRecorder implements FlowRecorder
{
    public function init(array $params): void
    {
    }

    public function record(string $requestId, FlowPayload $payload): void
    {
        $event = new FlowEvents();
        $event->request_id = $requestId;
        $event->payload = $payload;
        $event->save();
    }

    public function retrieve(Carbon $from, Carbon $to): Builder
    {
        return FlowEvents::query()
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to);
    }
}
