<?php

namespace EricPridham\Flow\Tests\Unit\Watchers;

use EricPridham\Flow\Recorder\DatabaseRecorder;
use EricPridham\Flow\Tests\FeatureTestCase;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;

class RequestWatcherTest extends FeatureTestCase
{
    /** @test */
    public function it_hears_a_request_event(): void
    {
        $this->artisan('migrate', ['--database' => 'testbench']);

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive([
            'fullUrl' => 'full-url',
            'method' => 'method',
            'all' => ['foo' => 'bar'],
            'is' => false,
        ]);

        $response = Mockery::mock(Response::class);
        $response->shouldReceive([
            'getContent' => json_encode(['response' => true])
        ]);

        event(new RequestHandled($request, $response));

        $events = (new DatabaseRecorder)->retrieve()->get();
        $this->assertCount(1, $events);
        $this->assertEquals('full-url', $events[0]->payload->data['url']);
        $this->assertEquals('method', $events[0]->payload->data['method']);
        $this->assertEquals(['foo' => 'bar'], $events[0]->payload->data['contents']);
        $this->assertEquals(['response' => true], $events[0]->payload->data['response']);
    }
}
