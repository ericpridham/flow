<?php

namespace EricPridham\Flow\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Interfaces\FlowWatcher;
use EricPridham\Flow\Payloads\ExceptionPayload;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;

class ExceptionWatcher implements FlowWatcher
{
    private static $exceptionsSeen;

    public function register(Flow $flow, array $params): void
    {
        Event::listen(MessageLogged::class, function (MessageLogged $event) use ($flow) {
            $exception = $event->context['exception'] ?? null;
            if (!$exception) {
                return;
            }

            if (self::seenException($exception)) {
                return;
            }
            self::markExceptionSeen($exception);

            $flow->record(ExceptionPayload::fromException($exception));
        });
    }

    /**
     * @param \Exception $exception
     */
    private static function markExceptionSeen(\Exception $exception): void
    {
        self::$exceptionsSeen[self::getExceptionKey($exception)] = true;
    }

    /**
     * @param \Exception $exception
     * @return bool
     */
    private static function seenException(\Exception $exception): bool
    {
        return isset(self::$exceptionsSeen[self::getExceptionKey($exception)]);
    }

    /**
     * @param \Exception $exception
     * @return string
     */
    private static function getExceptionKey(\Exception $exception): string
    {
        return get_class($exception);
    }
}
