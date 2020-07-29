<?php

namespace EricPridham\Flow\Interfaces;

use EricPridham\Flow\Flow;

interface FlowWatcher
{
    public function register(Flow $flow, array $params);
}
