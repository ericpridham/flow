<?php

namespace EricPridham\Flow;

use Illuminate\Http\Request;

class FlowHelpers
{
    public static function getAppStart(): ?float
    {
        return defined('LARAVEL_START') ? LARAVEL_START : null;
    }

    public static function calcDurationMs(float $start): float
    {
        return (microtime(true) - $start) * 1000;
    }

    public static function calcStart(int|float $durationMs): float
    {
        return microtime(true) - ($durationMs/1000);
    }
}