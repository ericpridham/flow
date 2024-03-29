<?php

namespace EricPridham\Flow\Recorder;

use Carbon\Carbon;
use EricPridham\Flow\Interfaces\FlowPayload;
use EricPridham\Flow\Models\FlowEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class DatabaseRecorder implements FlowRecorder
{
    private $path;
    private $middleware;

    /**
     * @var Collection
     */
    protected $events;

    public function init(array $params = []): void
    {
        $this->path = $params['path'] ?? 'flow';
        $this->middleware = $params['middleware'] ?? [];
        $this->events = collect();

        $this->loadRoutes();
    }

    public function record(string $requestId, FlowPayload $payload, Carbon $start = null, float $durationMs = 0.0): void
    {
        $event = new FlowEvents();
        $event->request_id = $requestId;
        $event->payload = $payload;
        $event->duration_ms = $durationMs;
        if ($start) {
            $event->created_at = $start;
        }
        $this->events->add($event);
    }

    public function retrieve(Carbon $from = null, Carbon $to = null): Builder
    {
        $subquery = FlowEvents::query();
        if ($from) {
            $subquery->where('created_at', '>=', $from);
        }
        if ($to) {
            $subquery->where('created_at', '<=', $to);
        }

        return FlowEvents::whereIn('request_id', $subquery->select('request_id'));
    }

    public function store()
    {
        if (!$this->events) {
            return;
        }

        $this->events->each(function ($event) { $event->save(); });
    }

    public function __destruct()
    {
        try {
            $this->store();
        } catch (\Exception $exception) {
        }
    }

    protected function loadRoutes()
    {
        Route::group([
            'namespace' => 'EricPridham\Flow\Http\Controllers',
            'prefix' => $this->path,
            'middleware' => $this->middleware
        ], function () {
            Route::get('/', 'FlowController@index');
            Route::get('/events', 'FlowController@events');
        });
    }
}
