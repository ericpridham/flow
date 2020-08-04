<?php

namespace EricPridham\Flow\Recorder;

use Carbon\Carbon;
use EricPridham\Flow\Interfaces\FlowPayload;
use EricPridham\Flow\Models\FlowEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DatabaseRecorder implements FlowRecorder
{
    /**
     * @var Collection
     */
    protected $events;

    public function init(array $params = []): void
    {
        $this->events = collect();
    }

    public function record(string $requestId, FlowPayload $payload, Carbon $at = null): void
    {
        $event = new FlowEvents();
        $event->request_id = $requestId;
        $event->payload = $payload;
        if ($at) {
            $event->created_at = $at;
        }
        $this->events->add($event);
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

    public function store()
    {
        if (!$this->events) {
            return;
        }

        $this->events->each(function ($event) { $event->save(); });
    }

    public function __destruct()
    {
        try {
            $this->store();
        } catch (Exception $exception) {
        }
    }
}
