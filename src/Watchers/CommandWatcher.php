<?php

namespace EricPridham\Flow\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Interfaces\FlowWatcher;
use EricPridham\Flow\Payloads\CommandPayload;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Event;

class CommandWatcher implements FlowWatcher
{
    public function register(Flow $flow, array $params)
    {
        Event::listen(CommandFinished::class, function (CommandFinished $event) use ($flow, $params) {
            $flow->record(CommandPayload::fromCommandFinished($event));
        });
    }
}
