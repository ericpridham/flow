<?php

namespace EricPridham\Flow;

use EricPridham\Flow\Interfaces\FlowPayload;
use EricPridham\Flow\Interfaces\FlowWatcher;
use EricPridham\Flow\Models\FlowEvents;
use Illuminate\Database\Eloquent\Builder;
use Ramsey\Uuid\Uuid;

class Flow
{
    private $request_id;

    public function __construct()
    {
        $this->request_id = Uuid::uuid4()->toString();
    }

    public function register(array $watchers): void
    {
        collect($watchers)->each(function ($watcher) {
            if (is_string($watcher)) {
                $watcherInstance = new $watcher();
            } elseif ($watcher instanceof FlowWatcher) {
                $watcherInstance = $watcher;
            }
            $watcherInstance->register($this);
        });
    }

    public function record(FlowPayload $payload): void
    {
        $event = new FlowEvents();
        $event->request_id = $this->request_id;
        $event->payload = $payload;
        $event->save();
    }

    public function retrieve(): Builder
    {
        return FlowEvents::orderByDesc('created_at');
    }
}
