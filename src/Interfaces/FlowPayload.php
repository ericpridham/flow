<?php

namespace EricPridham\Flow\Interfaces;

use Ramsey\Uuid\Uuid;

abstract class FlowPayload
{
    public $id;
    public $data;

    public function __construct(string $id = null, array $data = null)
    {
        $this->id = $id ?? Uuid::uuid4()->toString();
        $this->data = $data;
    }
}
