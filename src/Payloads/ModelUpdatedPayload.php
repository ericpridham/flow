<?php

namespace EricPridham\Flow\Payloads;

use EricPridham\Flow\Interfaces\FlowPayload;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;

class ModelUpdatedPayload extends FlowPayload
{
    public string $type = 'model';
    public string $color = '#56bb8d';

    public static function fromModel(Model $model): static
    {
        return new static(null, [
            'model_name' => get_class($model),
            'record' => $model->toArray(),
            'changes' => $model->getDirty(),
        ]);
    }

    public function getTitle(): string
    {
        return $this->data['model_name'] . ' Updated';
    }

    public function getDetails(): mixed
    {
        return [
            'changes' => $this->data['changes'],
            'record' => $this->data['record']
        ];
    }

    public function getTraceTags(): array
    {
        return [
            'model.event' => 'updated',
            'model.name' => $this->data['model_name'],
            'model.record' => $this->data['record'],
            'model.changes' => $this->data['changes'],
        ];
    }
}
