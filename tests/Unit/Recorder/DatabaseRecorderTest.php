<?php

namespace EricPridham\Flow\Tests\Unit\Recorder;

use EricPridham\Flow\Payloads\GenericPayload;
use EricPridham\Flow\Payloads\RequestPayload;
use EricPridham\Flow\Recorder\DatabaseRecorder;
use EricPridham\Flow\Tests\FeatureTestCase;

class DatabaseRecorderTest extends FeatureTestCase
{
    /** @test */
    public function it_can_store_a_generic_payload(): void
    {
        $this->artisan('migrate', ['--database' => 'testbench']);

        $payload = new GenericPayload('id', ['data' => 'foo']);

        $recorder = new DatabaseRecorder();
        $recorder->record('requestId', $payload);

        $events = $recorder->retrieve()->get();
        $this->assertCount(1, $events);

        $event = $events->first();

        $this->assertInstanceOf(GenericPayload::class, $event->payload);
        $this->assertEquals($payload->id, $event->payload->id);
        $this->assertEquals(['data' => 'foo'], $event->payload->data);
    }

    /** @test */
    public function it_can_store_a_custom_payload(): void
    {
        $this->artisan('migrate', ['--database' => 'testbench']);

        $payload = new RequestPayload('id', ['data' => 'foo']);

        $recorder = new DatabaseRecorder();
        $recorder->record('requestId', $payload);

        $events = $recorder->retrieve();

        $this->assertInstanceOf(RequestPayload::class, $events->first()->payload);
    }
}
