<?php

namespace EricPridham\Flow\Recorder;

use Carbon\Carbon;
use EricPridham\Flow\FlowHelpers;
use EricPridham\Flow\Interfaces\FlowPayload;

use Illuminate\Support\Facades\App;
use OpenTracing\Formats;
use OpenTracing\NoopTracer;
use OpenTracing\Tracer;

class OpenTracingRecorder implements FlowRecorder
{
    private Tracer $tracer;

    public function init(array $params): void
    {
        $spanName = $params['root'] ?? 'laravel';

        try {
            $tracer = App::make(Tracer::class);
        } catch (\Throwable $exception) {
            $tracer = new NoopTracer();
        }
        $this->tracer = $tracer;

        // fallback to empty array if getallheaders() isn't available
        $headers = function_exists('getallheaders') ? getallheaders() : [];

        // extract the span context
        $spanContext = $this->tracer->extract(
            Formats\TEXT_MAP,
            $headers
        );

        $scope = $this->tracer->startActiveSpan($spanName, [
            'child_of' => $spanContext,
            'start_time' => Carbon::createFromTimestamp(FlowHelpers::getAppStart())->getPreciseTimestamp(),
        ]);

        register_shutdown_function(function () use ($scope, $tracer) {
            $scope->close();
            $tracer->flush();
        });
    }

    public function record(string $requestId, FlowPayload $payload, Carbon $start = null, float $durationMs = 0.0): void
    {
        //TODO not sure if $durationMs can be force, so here it's unused
        $payload->addTraceData($this->tracer, $start);
    }
}