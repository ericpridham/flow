<?php

namespace EricPridham\Flow\Tests\Unit\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Payloads\RequestPayload;
use EricPridham\Flow\Recorder\FlowRecorder;
use EricPridham\Flow\Tests\FeatureTestCase;
use EricPridham\Flow\Watchers\RequestWatcher;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;

class RequestWatcherTest extends FeatureTestCase
{
    /** @test */
    public function it_hears_a_request_event(): void
    {
        $flow = new Flow();
        $recorder = Mockery::spy(FlowRecorder::class);
        $flow->registerRecorders([$recorder]);

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive([
            'fullUrl' => 'full-url',
            'method' => 'method',
            'all' => ['foo' => 'bar'],
            'is' => false,
            'server' => 0.0
        ]);

        $response = Mockery::mock(Response::class);
        $response->shouldReceive([
            'getContent' => json_encode(['response' => true])
        ]);

        $flow->registerWatchers([new RequestWatcher()]);

        event(new RequestHandled($request, $response));

        $recorder->shouldHaveReceived('record',
            function (string $requestId, RequestPayload $payload) {
                $this->assertEquals('full-url', $payload->data['url']);
                $this->assertEquals('method', $payload->data['method']);
                $this->assertEquals(['foo' => 'bar'], $payload->data['contents']);
                $this->assertEquals(['response' => true], $payload->data['response']);
                return true;
            }
        );
    }
}
