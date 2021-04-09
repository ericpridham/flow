<?php

namespace EricPridham\Flow\Recorder;

use Carbon\Carbon;
use EricPridham\Flow\Interfaces\FlowPayload;
use EricPridham\Flow\Models\FlowEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

        return FlowEvents::whereIn('request_id',
            // mysql does not allow you to directly reference a table in a subquery if the main query is a delete. for
            // instance:
            //
            // delete from t where id in (select id from t where ...) -- mysql error!
            //
            // to get around this, you must then subquery the subquery:
            //
            // delete from t where id in (select * from (select id from t where ...) as t_ids) -- mysql happy!
            //
            // This does that.
            DB::table(DB::raw("({$subquery->select('request_id')->toSql()}) as request_ids"))
                ->mergeBindings($subquery->getQuery())
        );
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
