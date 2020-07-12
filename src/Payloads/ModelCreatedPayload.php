<?php

namespace EricPridham\Flow\Payloads;

use EricPridham\Flow\Interfaces\FlowPayload;
use Illuminate\Database\Eloquent\Model;

class ModelCreatedPayload extends FlowPayload
{
    public $type = 'model';
    public $color = '#56bb8d';

    public static function fromModel(Model $model): ModelCreatedPayload
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

    public function getDetails()
    {
        return $this->data['record'];
    }
}
