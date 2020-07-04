<?php

namespace EricPridham\Flow\Recorder;

use EricPridham\Flow\Interfaces\FlowPayload;

interface FlowRecorder
{
    public function init();
    public function record(string $requestId, FlowPayload $payload);
}
