<?php

namespace EricPridham\Flow\Tests\Unit\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Payloads\ExceptionPayload;
use EricPridham\Flow\Recorder\FlowRecorder;
use EricPridham\Flow\Tests\FeatureTestCase;
use EricPridham\Flow\Watchers\ExceptionWatcher;
use Illuminate\Log\Events\MessageLogged;
use Mockery;

class ExceptionWatcherTest extends FeatureTestCase
{
    /** @test */
    public function it_hears_an_exception_event(): void
    {
        $flow = new Flow();
        $recorder = Mockery::spy(FlowRecorder::class);
        $flow->registerRecorders([$recorder]);

        $flow->registerWatchers([new ExceptionWatcher()]);

        $exception = new \Exception('message', 123);
        event(new MessageLogged(null, null, ['exception' => $exception]));

        $recorder->shouldHaveReceived('record',
            function (string $requestId, ExceptionPayload $payload) use ($exception) {
                $this->assertEquals('message', $payload->data['message']);
                $this->assertEquals('123', $payload->data['code']);
                $this->assertEquals($exception->getFile(), $payload->data['file']);
                $this->assertEquals($exception->getLine(), $payload->data['line']);
                $this->assertEquals($exception->getTraceAsString(), $payload->data['trace']);
                return true;
            }
        );
    }
}
