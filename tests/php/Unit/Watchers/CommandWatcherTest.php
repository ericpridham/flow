<?php

namespace EricPridham\Flow\Tests\Unit\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Payloads\CommandPayload;
use EricPridham\Flow\Recorder\FlowRecorder;
use EricPridham\Flow\Tests\FeatureTestCase;
use EricPridham\Flow\Watchers\CommandWatcher;
use Illuminate\Console\Events\CommandFinished;
use Mockery;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandWatcherTest extends FeatureTestCase
{
    /** @test */
    public function it_hears_a_finished_command(): void
    {
        $flow = new Flow();
        $recorder = Mockery::spy(FlowRecorder::class);
        $flow->registerRecorders([$recorder]);

        $flow->registerWatchers([new CommandWatcher()]);

        $input = Mockery::mock(InputInterface::class);
        $input->shouldReceive([
            'getArguments' => 'arguments',
            'getOptions' => 'options',
        ]);
        event(new CommandFinished('command', $input, Mockery::spy(OutputInterface::class), 3));

        $recorder->shouldHaveReceived('record',
            function (string $requestId, CommandPayload $payload) {
                $this->assertEquals('command', $payload->data['command']);
                $this->assertEquals('3', $payload->data['exit_code']);
                $this->assertEquals('arguments', $payload->data['arguments']);
                $this->assertEquals('options', $payload->data['options']);
                return true;
            }
        );
    }
}
