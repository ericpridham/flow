<?php

namespace EricPridham\Flow\Payloads;

use EricPridham\Flow\Interfaces\FlowPayload;
use Illuminate\Database\Eloquent\Model;

class ModelUpdatedPayload extends FlowPayload
{
    public $type = 'model';
    public $color = '#56bb8d';

    public static function fromModel(Model $model): ModelUpdatedPayload
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

    public function getDetails()
    {
        return [
            'changes' => $this->data['changes'],
            'record' => $this->data['record']
        ];
    }
}
