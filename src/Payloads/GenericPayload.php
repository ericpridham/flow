<?php


namespace EricPridham\Flow\Payloads;


use EricPridham\Flow\Interfaces\FlowPayload;

class GenericPayload extends FlowPayload
{
    public string $type = 'generic';
}
