<?php

namespace EricPridham\Flow\Tests\Unit\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Models\FlowEvents;
use EricPridham\Flow\Payloads\ModelCreatedPayload;
use EricPridham\Flow\Recorder\FlowRecorder;
use EricPridham\Flow\Tests\FeatureTestCase;
use EricPridham\Flow\Watchers\ModelWatcher;
use Illuminate\Database\Eloquent\Model;
use Mockery;

class ModelWatcherTest extends FeatureTestCase
{
    /** @test */
    public function it_hears_a_model_created_event(): void
    {
        $flow = new Flow();
        $recorder = Mockery::spy(FlowRecorder::class);
        $flow->registerRecorders([$recorder]);

        $flow->registerWatchers([new ModelWatcher()]);

        $model = Mockery::mock(OtherModel::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $model->shouldReceive(['insertAndSetId' => null]);
        $model->save();

        $recorder->shouldHaveReceived('record',
            function (string $requestId, ModelCreatedPayload $payload) use ($model) {
                $this->assertEquals(get_class($model), $payload->data['model_name']);
                $this->assertArrayHasKey('created_at', $payload->data['record']);
                $this->assertArrayHasKey('updated_at', $payload->data['record']);
                return true;
            }
        );
    }

    /** @test */
    public function it_always_filters_flow_events(): void
    {
        $this->artisan('migrate', ['--database' => 'testbench']);

        $flow = new Flow();
        $recorder = Mockery::spy(FlowRecorder::class);
        $flow->registerRecorders([$recorder]);

        $flow->registerWatchers([
            ModelWatcher::class
        ]);

        $event = FlowEvents::create([
            'request_id' => '12345',
            'event_id' => '12345',
            'payload_class' => '12345',
            'payload_data' => '12345',
        ]);

        $recorder->shouldNotHaveReceived('record');

        $event->event_id = '67890';
        $event->save();

        $recorder->shouldNotHaveReceived('record');
    }

    /** @test */
    public function it_filters_models_from_settings(): void
    {
        $flow = new Flow();
        $recorder = Mockery::spy(FlowRecorder::class);
        $flow->registerRecorders([$recorder]);

        $flow->registerWatchers([
            ModelWatcher::class => [
                'filter' => [
                    OtherModel::class
                ]
            ]
        ]);

        $model = Mockery::mock(OtherModel::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $model->shouldReceive(['insertAndSetId' => null]);
        $model->save();

        $recorder->shouldNotHaveReceived('record');
    }
}

class OtherModel extends Model
{
}
