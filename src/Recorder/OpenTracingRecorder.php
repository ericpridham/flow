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
        $spanName = $params['rootSpan'] ?? 'laravel';

        $this->tracer = $tracer = App::make(Tracer::class) ?? new NoopTracer();

        // extract the span context
        $spanContext = $this->tracer->extract(
            Formats\TEXT_MAP,
            getallheaders()
        );

        $scope = $this->tracer->startActiveSpan($spanName, [
            'child_of' => $spanContext,
            'start_time' => Carbon::createFromTimestamp(FlowHelpers::getAppStart())->getPreciseTimestamp(),
        ]);

        register_shutdown_function(function () use ($scope, $tracer) {
            $scope->close();
            /* Flush the tracer to the backend */
            $tracer->flush();
        });
    }

    public function record(string $requestId, FlowPayload $payload, Carbon $start = null, float $durationMs = 0.0): void
    {
        $options = [];
        if ($start) {
            $options['start_time'] = $start->getPreciseTimestamp();
        }

        $scope = $this->tracer->startActiveSpan($payload->type, $options);

        foreach ($payload->data as $key => $value) {
            $scope->getSpan()->setTag($key, print_r($value, true));
        }

        //TODO not sure if $durationMs can be force, so here it's unused
        $scope->close();
    }

    public function loadRoutes()
    {
    }
}