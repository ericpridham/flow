<?php

namespace EricPridham\Flow\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Payloads\QueryPayload;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

class QueryWatcher
{
    public function register(Flow $flow): void
    {
        Event::listen(QueryExecuted::class, function (QueryExecuted $event) use ($flow) {
            if (Str::is('*flow_events*', $event->sql)) {
                return;
            }
            $flow->record(QueryPayload::fromQueryExecuted($event));
        });
    }
}
