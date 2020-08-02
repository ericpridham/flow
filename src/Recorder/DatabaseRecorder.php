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

    public function record(string $requestId, FlowPayload $payload, Carbon $at = null): void
    {
        $event = new FlowEvents();
        $event->request_id = $requestId;
        $event->payload = $payload;
        if ($at) {
            $event->created_at = $at;
        }
        $event->save();
    }

    public function retrieve(Carbon $from = null, Carbon $to = null): Builder
    {
        $subquery = FlowEvents::query();
        if ($from) {
            $subquery->where('created_at', '>=', $from);
        }
        if ($to) {
            $subquery->where('created_at', '<=', $to);
        }

        return FlowEvents::whereIn('request_id', $subquery->select('request_id'));
    }
}
