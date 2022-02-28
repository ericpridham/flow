<?php

namespace EricPridham\Flow;

use Carbon\Carbon;
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
        collect($watchers)->each(function ($value, $key) {
            if (is_string($key)) {
                $registerParams = $value;
                $watcher = $key;
            } else {
                $registerParams = [];
                $watcher = $value;
            }

            if (is_string($watcher)) {
                $watcherInstance = new $watcher();
            } elseif ($watcher instanceof FlowWatcher) {
                $watcherInstance = $watcher;
            }
            $watcherInstance->register($this, $registerParams);
        });
    }

    public function registerRecorders(array $recorders): void
    {
        collect($recorders)->each(function ($value, $key) {
            if (is_string($key)) {
                $initParams = $value;
                $recorder = $key;
            } else {
                $initParams = [];
                $recorder = $value;
            }

            if (is_string($recorder)) {
                $recorderInstance = new $recorder();
            } elseif ($recorder instanceof FlowRecorder) {
                $recorderInstance = $recorder;
            }
            $recorderInstance->init($initParams);
            $this->recorders->push($recorderInstance);
        });
    }

    public function record(FlowPayload $payload, Carbon|float $start = null, float $durationMs = 0.0): void
    {
        if (is_float($start)) {
            $at = Carbon::createFromTimestamp($start);
        } else {
            $at = $start;
        }

        $this->recorders->each(function ($recorder) use ($payload, $at, $durationMs) {
            $recorder->record($this->request_id, $payload, $at, $durationMs);
        });
    }

    public function getRecorders(): Collection
    {
        return $this->recorders;
    }
}
