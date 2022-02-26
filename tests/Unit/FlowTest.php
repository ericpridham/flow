<?php

namespace EricPridham\Flow\Tests\Unit;

use Carbon\Carbon;
use EricPridham\Flow\Flow;
use EricPridham\Flow\Interfaces\FlowPayload;
use EricPridham\Flow\Interfaces\FlowWatcher;
use EricPridham\Flow\Recorder\FlowRecorder;
use EricPridham\Flow\Recorder\RecordContext;
use EricPridham\Flow\Tests\FeatureTestCase;
use Mockery;

class FlowTest extends FeatureTestCase
{
    /** @test */
    public function it_can_register_watchers(): void
    {
        $watcher = Mockery::spy(FlowWatcher::class);

        $flow = new Flow();
        $flow->registerWatchers([
            $watcher,
            TestWatcher::class
        ]);

        $this->assertTrue(TestWatcher::$registered);

        $watcher->shouldHaveReceived('register');

        TestWatcher::reset();
        $this->assertFalse(TestWatcher::$registered);

        $flow->registerWatchers([
            TestWatcher::class => [
                'key' => 'value',
            ]
        ]);
        $this->assertEquals(['key' => 'value'], TestWatcher::$lastRegisterParams);
    }

    /** @test */
    public function it_can_register_recorders(): void
    {
        $recorder = Mockery::spy(FlowRecorder::class);

        $flow = new Flow();
        $flow->registerRecorders([
            $recorder,
            TestRecorder::class,
        ]);

        $recorder->shouldHaveReceived('init');
        $this->assertTrue($flow->getRecorders()->contains($recorder));
        $this->assertTrue(TestRecorder::$initialized);

        TestRecorder::reset();
        $this->assertFalse(TestRecorder::$initialized);

        $flow->registerRecorders([
            TestRecorder::class => [
                'key' => 'value',
            ]
        ]);
        $this->assertEquals(['key' => 'value'], TestRecorder::$lastInitParams);
    }
}

class TestWatcher implements FlowWatcher
{
    static public $lastRegisterParams;
    static public $registered = false;

    public static function reset(): void
    {
        self::$lastRegisterParams = null;
        self::$registered = false;
    }

    public function register(Flow $flow, $params = null)
    {
        self::$registered = true;
        self::$lastRegisterParams = $params;
    }
}

class TestRecorder implements FlowRecorder
{
    static public $lastInitParams;
    static public $initialized = false;

    public static function reset(): void
    {
        self::$lastInitParams = null;
        self::$initialized = false;
    }

    public function init($params = null): void
    {
        self::$initialized = true;
        self::$lastInitParams = $params;
    }

    public function record(string $requestId, FlowPayload $payload, Carbon $at = null, int $durationMs = 0): void
    {
        // TODO: Implement record() method.
    }

    public function loadRoutes()
    {
        // TODO: Implement loadRoutes() method.
    }

    public function recordStart(): RecordContext
    {
        return new RecordContext();
        // TODO: Implement recordStart() method.
    }

    public function recordFinish(string $requestId, FlowPayload $payload, RecordContext $context): void
    {
        // TODO: Implement recordFinish() method.
    }
}
