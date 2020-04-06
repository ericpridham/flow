<?php

namespace EricPridham\Flow\Models;

use EricPridham\Flow\Interfaces\FlowPayload;
use EricPridham\Flow\Payloads\GenericPayload;
use Illuminate\Database\Eloquent\Model;

class FlowEvents extends Model
{
    protected $casts = [
        'payload_data' => 'array',
    ];

    public function getPayloadAttribute(): FlowPayload
    {
        $class = class_exists($this->payload_class)? $this->payload_class : GenericPayload::class;
        return new $class($this->event_id, $this->payload_data);
    }

    public function setPayloadAttribute(FlowPayload $payload): void
    {
        $this->event_id = $payload->id;
        $this->payload_class = get_class($payload);
        $this->payload_data = $payload->data;
    }
}
