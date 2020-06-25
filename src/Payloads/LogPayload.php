<?php


namespace EricPridham\Flow\Payloads;


use EricPridham\Flow\Interfaces\FlowPayload;
use Illuminate\Log\Events\MessageLogged;

class LogPayload extends FlowPayload
{
    public $type = 'log';

    public static function fromMessageLogged(MessageLogged $event)
    {
        return new static(null, [
            'level' => $event->level,
            'message' => $event->message,
            'context' => $event->context,
        ]);
    }
}
