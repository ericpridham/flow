<?php

namespace EricPridham\Flow\Payloads;

use EricPridham\Flow\Interfaces\FlowPayload;
use Illuminate\Console\Events\CommandFinished;

class CommandPayload extends FlowPayload
{
    public $type = 'command';
    public $color = 'grey';

    public static function fromCommandFinished(CommandFinished $event)
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
