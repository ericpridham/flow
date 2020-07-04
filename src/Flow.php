<?php

namespace EricPridham\Flow;

use EricPridham\Flow\Interfaces\FlowPayload;
use EricPridham\Flow\Interfaces\FlowWatcher;
use EricPridham\Flow\Recorder\FlowRecorder;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

class Flow
{
    private $request_id;
    private $recorders;

    public function __construct()
    {
        $this->request_id = Uuid::uuid4()->toString();
        $this->recorders = collect();
    }

    public function registerWatchers(array $watchers): void
    {
        collect($watchers)->each(function ( $watcher) {
            if (is_string($watcher)) {
                $watcherInstance = new $watcher();
            } elseif ($watcher instanceof FlowWatcher) {
                $watcherInstance = $watcher;
            }
            $watcherInstance->register($this);
        });
    }

    public function registerRecorders(array $recorders): void
    {
        collect($recorders)->each(function ($recorder) {
            if (is_string($recorder)) {
                $recorderInstance = new $recorder();
            } elseif ($recorder instanceof FlowRecorder) {
                $recorderInstance = $recorder;
            }
            $recorderInstance->init();
            $this->recorders->push($recorderInstance);
        });
    }

    public function record(FlowPayload $payload): void
    {
        $this->recorders->each(function ($recorder) use ($payload) {
            $recorder->record($this->request_id, $payload);
        });
    }

    public function getRecorders(): Collection
    {
        return $this->recorders;
    }
}
