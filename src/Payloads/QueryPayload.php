<?php

namespace EricPridham\Flow\Payloads;

use EricPridham\Flow\Interfaces\FlowPayload;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Str;

class QueryPayload extends FlowPayload
{
    public $type = 'model';
    public $color = 'orange';

    public static function fromQueryExecuted(QueryExecuted $event): QueryPayload
    {
        return new static(null, [
            'sql' => $event->sql,
            'bindings' => $event->connection->prepareBindings($event->bindings),
            'time' => $event->time,
        ]);
    }

    public function getTitle(): string
    {
        return 'Query: ' . Str::limit($this->data['sql'], 100) . '  (' . ($this->data['time']??'??') . 'ms)';
    }

    public function getDetails()
    {
        return $this->data;
    }
}
