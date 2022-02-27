<?php

namespace EricPridham\Flow\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\FlowHelpers;
use EricPridham\Flow\Interfaces\FlowWatcher;
use EricPridham\Flow\Models\FlowEvents;
use EricPridham\Flow\Payloads\QueryPayload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

class QueryWatcher implements FlowWatcher
{
    public function register(Flow $flow, array $params): void
    {
        Event::listen(QueryExecuted::class, function (QueryExecuted $event) use ($flow, $params) {
            $patterns = collect($params['filter'] ?? [])
                ->push(FlowEvents::class) // never record FlowEvents queries
                ->map(function ($param) {
                    return '*"' . $this->getTableName($param) . '"*';
                })->toArray();
            if (Str::is($patterns, $event->sql)) {
                return;
            }

            $durationMs = $event->time;
            $start = FlowHelpers::calcStart($durationMs);

            $flow->record(QueryPayload::fromQueryExecuted($event), $start, $durationMs);
        });
    }

    /**
     * @param $param
     * @return mixed
     */
    public function getTableName($param)
    {
        if (class_exists($param)) {
            $model = new $param;
            if (!($model instanceof Model)) {
                throw new \RuntimeException("Must be a model class [$param]");
            }
            $table = (new $param)->getTable();
        } else {
            $table = $param;
        }
        return $table;
    }
}
