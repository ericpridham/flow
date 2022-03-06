<?php

namespace EricPridham\Flow\Interfaces;

use Carbon\Carbon;
use OpenTracing\Tracer;
use Ramsey\Uuid\Uuid;

/**
 * An object to store the payload details for FlowEvent model records.
 */
abstract class FlowPayload
{
    public string $id;
    public ?array $data;
    public string $type = 'unknown';
    public string $color = "#757575";

    public function __construct(string $id = null, array $data = null)
    {
        $this->id = $id ?? Uuid::uuid4()->toString();
        $this->data = $data;
    }

    public function getTitle(): string
    {
        return ucfirst($this->type);
    }

    public function getDetails(): mixed
    {
        return $this->data;
    }

    /**
     * @param Carbon|null $start
     * @param Tracer $tracer
     * @return void
     */
    public function addTraceData(Tracer $tracer, ?Carbon $start): void
    {
        $options = [];
        if ($start) {
            $options['start_time'] = $start->getPreciseTimestamp();
        }

        $scope = $tracer->startActiveSpan($this->type, $options);
        $span = $scope->getSpan();

        foreach ($this->getTraceTags() as $key => $value) {
            if (is_array($value)) {
                $span->setTag($key, json_encode($value));
            } else {
                $span->setTag($key, $value);
            }
        }

        $scope->close();
    }

    public function getTraceTags(): array
    {
        return $this->data;
    }
}
