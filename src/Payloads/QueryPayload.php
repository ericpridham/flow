<?php

namespace EricPridham\Flow\Payloads;

use EricPridham\Flow\Interfaces\FlowPayload;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Str;

class QueryPayload extends FlowPayload
{
    public string $type = 'query';
    public string $color = 'orange';

    public static function fromQueryExecuted(QueryExecuted $event): static
    {
        return new static(null, [
            'sql' => $event->sql,
            'bindings' => $event->connection->prepareBindings($event->bindings),
            'time' => $event->time,
            'driver' => $event->connection->getDriverName(),
            'database' => $event->connection->getDatabaseName(),
        ]);
    }

    public function getTitle(): string
    {
        return 'Query: ' . Str::limit($this->data['sql'], 100) . '  (' . ($this->data['time']??'??') . 'ms)';
    }

    public function getDetails(): mixed
    {
        return $this->data;
    }

    public function getTraceTags(): array
    {
        return [
            'db.name' => $this->data['database'],
            'db.type' => $this->data['driver'],
            'db.statement' => $this->data['sql'],
            'db.bindings' => json_encode($this->data['bindings']),
        ];
    }
}
