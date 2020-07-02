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

    public function getTypeAttribute()
    {
        $parts = explode('\\', $this->payload_class);
        return $parts[array_key_last($parts)];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'request_id' => $this->request_id,
            'type' => $this->payload->type,
            'timestamp' => $this->created_at->format('Y-m-d g:i:s A T'),
            'title' => $this->payload->getTitle(),
            'details' => $this->payload->getDetails(),
            'color' => $this->payload->color,
        ];
    }
}
