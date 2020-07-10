<?php

namespace EricPridham\Flow\Recorder;

use EricPridham\Flow\Interfaces\FlowPayload;

interface FlowRecorder
{
    public function init(array $params): void;
    public function record(string $requestId, FlowPayload $payload): void;
}
