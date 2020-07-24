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
    public function register(Flow $flow): void
    {
        Event::listen('eloquent.created*', function ($name, $data) use ($flow) {
            foreach ($data as $model) {
                if ($model instanceof FlowEvents) {
                    return;
                }
                $flow->record(ModelCreatedPayload::fromModel($model));
            }
        });

        Event::listen('eloquent.updated*', function ($name, $data) use ($flow) {
            foreach ($data as $model) {
                if ($model instanceof FlowEvents) {
                    return;
                }
                $flow->record(ModelUpdatedPayload::fromModel($model));
            }
        });
    }
}
