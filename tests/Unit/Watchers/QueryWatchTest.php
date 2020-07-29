<?php

namespace EricPridham\Flow\Tests\Unit\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Payloads\QueryPayload;
use EricPridham\Flow\Recorder\FlowRecorder;
use EricPridham\Flow\Tests\FeatureTestCase;
use EricPridham\Flow\Watchers\QueryWatcher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Mockery;

class QueryWatchTest extends FeatureTestCase
{
    /** @test */
    public function it_hears_an_exception_event(): void
    {
        $flow = new Flow();
        $recorder = Mockery::spy(FlowRecorder::class);
        $flow->registerRecorders([$recorder]);

        $flow->registerWatchers([new QueryWatcher()]);

        $connection = Mockery::mock();
        $bindings = ['foo', 'bar'];
        $connection->shouldReceive([
            'prepareBindings' => $bindings,
            'getName' => 'foo'
        ]);
        event(new QueryExecuted('sql', $bindings, 12345, $connection));

        $recorder->shouldHaveReceived('record',
            function (string $requestId, QueryPayload $payload) {
                $this->assertEquals('sql', $payload->data['sql']);
                $this->assertEquals(['foo', 'bar'], $payload->data['bindings']);
                return true;
            }
        );
    }

    /** @test */
    public function it_always_filters_flow_events(): void
    {
        $flow = new Flow();
        $recorder = Mockery::spy(FlowRecorder::class);
        $flow->registerRecorders([$recorder]);

        $flow->registerWatchers([
            QueryWatcher::class
        ]);

        event(new QueryExecuted('insert into "flow_events" values ()', null, null, Mockery::spy()));

        $recorder->shouldNotHaveReceived('record');
    }

    /** @test */
    public function it_filters_queries_from_settings(): void
    {
        $flow = new Flow();
        $recorder = Mockery::spy(FlowRecorder::class);
        $flow->registerRecorders([$recorder]);

        $flow->registerWatchers([
            QueryWatcher::class => [
                'filter' => [
                    'some_table',
                    SomeModel::class,
                ]
            ]
        ]);

        event(new QueryExecuted('insert into "some_table" values ()', null, null, Mockery::spy()));
        $recorder->shouldNotHaveReceived('record');

        event(new QueryExecuted('select * from "some_models"', null, null, Mockery::spy()));
        $recorder->shouldNotHaveReceived('record');
    }
}

class SomeModel extends Model
{

}
