<?php

namespace EricPridham\Flow\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Payloads\LogPayload;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;

class LogWatcher
{
    public function register(Flow $flow): void
    {
        Event::listen(MessageLogged::class, function (MessageLogged $event) use ($flow) {
            if (!empty($event->context['exception'])) {
                return;
            }

            $flow->record(LogPayload::fromMessageLogged($event));
        });
    }
}
