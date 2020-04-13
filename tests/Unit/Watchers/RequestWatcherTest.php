<?php

namespace EricPridham\Flow\Tests\Unit\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Tests\FeatureTestCase;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        ]);

        event(new RequestHandled($request, \Mockery::spy(Response::class)));

        $events = (new Flow)->retrieve();
        $this->assertCount(1, $events);
        $this->assertEquals('full-url', $events[0]->payload->data['url']);
        $this->assertEquals('method', $events[0]->payload->data['method']);
    }
}
