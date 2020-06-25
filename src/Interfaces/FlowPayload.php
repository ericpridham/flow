<?php

namespace EricPridham\Flow\Interfaces;

use Ramsey\Uuid\Uuid;

abstract class FlowPayload
{
    public $id;
    public $data;
    public $type = 'unknown';

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
