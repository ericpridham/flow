<?php

namespace EricPridham\Flow\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Interfaces\FlowWatcher;
use EricPridham\Flow\Payloads\LogPayload;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;

class LogWatcher implements FlowWatcher
{
    public function register(Flow $flow, array $params): void
    {
        Event::listen(MessageLogged::class, function (MessageLogged $event) use ($flow) {
            if (!empty($event->context['exception'])) {
                return;
            }

            $flow->record(LogPayload::fromMessageLogged($event));
        });
    }
}
