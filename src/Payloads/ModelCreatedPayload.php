<?php

namespace EricPridham\Flow\Payloads;

use EricPridham\Flow\Interfaces\FlowPayload;
use Illuminate\Database\Eloquent\Model;

class ModelCreatedPayload extends FlowPayload
{
    public string $type = 'model';
    public string $color = '#56bb8d';

    public static function fromModel(Model $model): static
    {
        return new static(null, [
            'model_name' => get_class($model),
            'record' => $model->toArray(),
        ]);
    }

    public function getTitle(): string
    {
        return $this->data['model_name'] . ' Created';
    }

    public function getDetails(): mixed
    {
        return $this->data['record'];
    }

    public function getTraceTags(): array
    {
        return [
            'model.event' => 'created',
            'model.name' => $this->data['model_name'],
            'model.record' => $this->data['record'],
        ];
    }
}
