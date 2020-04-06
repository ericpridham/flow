<?php

namespace EricPridham\Flow;

use EricPridham\Flow\Interfaces\FlowPayload;
use EricPridham\Flow\Models\FlowEvents;
use Illuminate\Support\Collection;
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
        collect($watchers)->each(function ($watcher) { $watcher->register($this); });
    }

    public function record(FlowPayload $payload): void
    {
        $event = new FlowEvents();
        $event->request_id = $this->request_id;
        $event->payload = $payload;
        $event->save();
    }

    public function retrieve(): Collection
    {
        return FlowEvents::all();
    }
}
