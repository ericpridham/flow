<?php

namespace EricPridham\Flow\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Interfaces\FlowWatcher;
use EricPridham\Flow\Models\FlowEvents;
use EricPridham\Flow\Payloads\ModelCreatedPayload;
use EricPridham\Flow\Payloads\ModelUpdatedPayload;
use Illuminate\Support\Facades\Event;

class ModelWatcher implements FlowWatcher
{
    public function register(Flow $flow, array $params): void
    {
        Event::listen('eloquent.created*', function ($name, $data) use ($flow, $params) {
            foreach ($data as $model) {
                if ($this->isFiltered($model, $params)) {
                    return;
                }
                $flow->record(ModelCreatedPayload::fromModel($model));
            }
        });

        Event::listen('eloquent.updated*', function ($name, $data) use ($flow, $params) {
            foreach ($data as $model) {
                if ($this->isFiltered($model, [])) {
                    return;
                }
                $flow->record(ModelUpdatedPayload::fromModel($model));
            }
        });
    }

    /**
     * @param $model
     * @param $params
     * @return bool
     */
    public function isFiltered($model, $params): bool
    {
        return collect($params['filter']??[])->push(FlowEvents::class)->contains(function($class) use ($model) {
            return $model instanceof $class;
        });
    }
}
