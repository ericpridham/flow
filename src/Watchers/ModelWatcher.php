<?php

namespace EricPridham\Flow\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Models\FlowEvents;
use EricPridham\Flow\Payloads\ModelCreatedPayload;
use EricPridham\Flow\Payloads\ModelUpdatedPayload;
use Illuminate\Support\Facades\Event;

class ModelWatcher
{
    public function register(Flow $flow): void
    {
        Event::listen('eloquent.created*', static function ($name, $data) use ($flow) {
            if ($data[0] instanceof FlowEvents) {
                return;
            }
            $flow->record(ModelCreatedPayload::fromModel($data[0]));
        });

        Event::listen('eloquent.updated*', static function ($name, $data) use ($flow) {
            if ($data[0] instanceof FlowEvents) {
                return;
            }
            $flow->record(ModelUpdatedPayload::fromModel($data[0]));
        });
    }
}
