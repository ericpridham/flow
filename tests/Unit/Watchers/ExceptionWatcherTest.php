<?php

namespace EricPridham\Flow\Tests\Unit\Watchers;

use EricPridham\Flow\Recorder\DatabaseRecorder;
use EricPridham\Flow\Tests\FeatureTestCase;
use Illuminate\Log\Events\MessageLogged;

class ExceptionWatcherTest extends FeatureTestCase
{
    /** @test */
    public function it_hears_an_exception_event(): void
    {
        $this->artisan('migrate', ['--database' => 'testbench']);

        $exception = new \Exception('message', 123);
        event(new MessageLogged(null, null, ['exception' => $exception]));

        $events = (new DatabaseRecorder)->retrieve()->get();
        $this->assertCount(1, $events);

        $this->assertEquals('message', $events[0]->payload->data['message']);
        $this->assertEquals(123, $events[0]->payload->data['code']);
        $this->assertEquals($exception->getFile(), $events[0]->payload->data['file']);
        $this->assertEquals($exception->getLine(), $events[0]->payload->data['line']);
        $this->assertEquals($exception->getTraceAsString(), $events[0]->payload->data['trace']);
    }
}
