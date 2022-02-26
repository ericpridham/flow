<?php

namespace EricPridham\Flow\Recorder;

use Carbon\Carbon;
use EricPridham\Flow\Interfaces\FlowPayload;

interface FlowRecorder
{
    public function init(array $params): void;
    public function record(string $requestId, FlowPayload $payload, Carbon $at = null, int $durationMs = 0): void;
    public function recordStart(): RecordContext;
    public function recordFinish(string $requestId, FlowPayload $payload, RecordContext $context): void;
    public function loadRoutes();
}
