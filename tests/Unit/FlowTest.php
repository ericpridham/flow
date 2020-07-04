<?php

namespace EricPridham\Flow\Tests\Unit;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Interfaces\FlowWatcher;
use EricPridham\Flow\Recorder\FlowRecorder;
use EricPridham\Flow\Tests\FeatureTestCase;
use Mockery;

class FlowTest extends FeatureTestCase
{
    /** @test */
    public function it_can_register_watchers(): void
    {
        $watcher = Mockery::spy(FlowWatcher::class);

        $flow = new Flow();
        $flow->registerWatchers([$watcher]);

        $watcher->shouldHaveReceived('register');
    }

    /** @test */
    public function it_can_register_recorders(): void
    {
        $recorder = Mockery::spy(FlowRecorder::class);

        $flow = new Flow();
        $flow->registerRecorders([$recorder]);

        $recorder->shouldHaveReceived('init');
        $this->assertTrue($flow->getRecorders()->contains($recorder));
    }
}
