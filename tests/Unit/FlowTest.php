<?php

namespace EricPridham\Flow\Tests\Unit;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Interfaces\FlowWatcher;
use EricPridham\Flow\Payloads\GenericPayload;
use EricPridham\Flow\Payloads\RequestPayload;
use EricPridham\Flow\Tests\FeatureTestCase;
use Mockery;

class FlowTest extends FeatureTestCase
{
    /** @test */
    public function it_can_register_watchers(): void
    {
        $watcher = Mockery::spy(FlowWatcher::class);

        $flow = new Flow();
        $flow->register([$watcher]);

        $watcher->shouldHaveReceived('register');
    }

    /** @test */
    public function it_can_store_a_generic_payload(): void
    {
        $this->artisan('migrate', ['--database' => 'testbench']);

        $payload = new GenericPayload('id', ['data' => 'foo']);


        $flow = new Flow();
        $flow->record($payload);

        $events = $flow->retrieve();
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

        $flow = new Flow();
        $flow->record($payload);

        $events = $flow->retrieve();

        $this->assertInstanceOf(RequestPayload::class, $events->first()->payload);
    }
}
