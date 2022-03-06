<?php

namespace EricPridham\Flow\Payloads;

use EricPridham\Flow\Interfaces\FlowPayload;
use Illuminate\Console\Events\CommandFinished;

class CommandPayload extends FlowPayload
{
    public string $type = 'command';
    public string $color = 'grey';

    public static function fromCommandFinished(CommandFinished $event): static
    {
        return new static(null, [
            'command' => $event->command,
            'exit_code' => $event->exitCode,
            'arguments' => $event->input->getArguments(),
            'options' => $event->input->getOptions(),
        ]);
    }

    public function getTitle(): string
    {
        return 'Command: '
            . $this->data['command']
            . ' <span style="opacity: 0.7;">exit ' . $this->data['exit_code'] . '</span>';
    }
}
