<?php

namespace EricPridham\Flow\Recorder;

use Carbon\Carbon;
use EricPridham\Flow\Interfaces\FlowPayload;

interface FlowRecorder
{
    public function init(array $params): void;
    public function record(string $requestId, FlowPayload $payload, Carbon $start = null, float $durationMs = 0.0): void;
    public function loadRoutes();
}
