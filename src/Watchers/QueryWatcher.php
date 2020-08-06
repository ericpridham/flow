<?php

namespace EricPridham\Flow\Watchers;

use EricPridham\Flow\Flow;
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
                ->push(FlowEvents::class)
                ->map(function ($param) {
                    return '*' . $this->getTableName($param) . '*';
                })->toArray();
            if (Str::is($patterns, $event->sql)) {
                return;
            }
            $flow->record(QueryPayload::fromQueryExecuted($event));
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
