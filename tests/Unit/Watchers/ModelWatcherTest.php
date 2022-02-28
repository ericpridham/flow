<?php

namespace EricPridham\Flow\Tests\Unit\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Models\FlowEvents;
use EricPridham\Flow\Payloads\ModelCreatedPayload;
use EricPridham\Flow\Recorder\FlowRecorder;
use EricPridham\Flow\Tests\DatabaseTestCase;
use EricPridham\Flow\Tests\FeatureTestCase;
use EricPridham\Flow\Watchers\ModelWatcher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mockery;

class ModelWatcherTest extends DatabaseTestCase
{
    /** @test */
    public function it_hears_a_model_created_event(): void
    {
        $flow = new Flow();
        $recorder = Mockery::spy(FlowRecorder::class);
        $flow->registerRecorders([$recorder]);

        $flow->registerWatchers([new ModelWatcher()]);

        $model = FakeFlowEvents::factory()->make();
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
                    FakeFlowEvents::class
                ]
            ]
        ]);

//        $model = Mockery::mock(FakeFlowEvents::class)->makePartial()->shouldAllowMockingProtectedMethods();
//        $model->shouldReceive(['insertAndSetId' => null]);
        $model = FakeFlowEvents::factory()->make();
        $model->save();

        $recorder->shouldNotHaveReceived('record');
    }
}

/*
 * We can just test using a FlowEvents because we explicitly filter out FlowEvents eloquent actions from being recorded
 * in flow events.
 */
class FakeFlowEvents extends Model
{
    use HasFactory;

    protected $table = 'flow_events';

    protected static function newFactory()
    {
        return new class extends Factory {
            protected $model = FakeFlowEvents::class;
            public function definition()
            {
                return [
                    'request_id' => $this->faker->uuid,
                    'event_id' => $this->faker->uuid,
                    'payload_class' => 'unknown',
                    'payload_data' => '{}',
                ];
            }
        };
    }
}
