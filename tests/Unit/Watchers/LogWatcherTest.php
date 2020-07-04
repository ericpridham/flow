<?php

namespace EricPridham\Flow\Tests\Unit\Watchers;

use EricPridham\Flow\Recorder\DatabaseRecorder;
use EricPridham\Flow\Tests\FeatureTestCase;
use Illuminate\Log\Events\MessageLogged;

class LogWatcherTest extends FeatureTestCase
{
    /** @test */
    public function it_hears_a_log_event(): void
    {
        $this->artisan('migrate', ['--database' => 'testbench']);

        event(new MessageLogged('info', 'my log message', ['context' => true]));

        $events = (new DatabaseRecorder)->retrieve()->get();
        $this->assertCount(1, $events);
        $this->assertEquals('info', $events[0]->payload->data['level']);
        $this->assertEquals('my log message', $events[0]->payload->data['message']);
        $this->assertEquals(['context' => true], $events[0]->payload->data['context']);
    }
}
