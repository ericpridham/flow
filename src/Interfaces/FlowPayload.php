<?php

namespace EricPridham\Flow\Interfaces;

use Ramsey\Uuid\Uuid;

/**
 * An object to store the payload details for FlowEvent model records.
 */
abstract class FlowPayload
{
    public $id;
    public $data;
    public $type = 'unknown';
    public $color = "#757575";

    public function __construct(string $id = null, array $data = null)
    {
        $this->id = $id ?? Uuid::uuid4()->toString();
        $this->data = $data;
    }

    public function getTitle(): string
    {
        return ucfirst($this->type);
    }

    public function getDetails()
    {
        return $this->data;
    }
}
