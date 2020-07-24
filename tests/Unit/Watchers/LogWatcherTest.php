<?php

namespace EricPridham\Flow\Tests\Unit\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Payloads\LogPayload;
use EricPridham\Flow\Recorder\FlowRecorder;
use EricPridham\Flow\Tests\FeatureTestCase;
use EricPridham\Flow\Watchers\LogWatcher;
use Illuminate\Log\Events\MessageLogged;
use Mockery;

class LogWatcherTest extends FeatureTestCase
{
    /** @test */
    public function it_hears_a_log_event(): void
    {
        $flow = new Flow();
        $recorder = Mockery::spy(FlowRecorder::class);
        $flow->registerRecorders([$recorder]);

        $flow->registerWatchers([new LogWatcher()]);

        event(new MessageLogged('info', 'my log message', ['context' => true]));

        $recorder->shouldHaveReceived('record',
            function (string $requestId, LogPayload $payload) {
                $this->assertEquals('info', $payload->data['level']);
                $this->assertEquals('my log message', $payload->data['message']);
                $this->assertEquals(['context' => true], $payload->data['context']);
                return true;
            }
        );
    }
}
