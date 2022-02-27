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
     * @param \Throwable $exception
     */
    private static function markExceptionSeen(\Throwable $exception): void
    {
        self::$exceptionsSeen[self::getExceptionKey($exception)] = true;
    }

    /**
     * @param \Throwable $exception
     * @return bool
     */
    private static function seenException(\Throwable $exception): bool
    {
        return isset(self::$exceptionsSeen[self::getExceptionKey($exception)]);
    }

    /**
     * @param \Throwable $exception
     * @return string
     */
    private static function getExceptionKey(\Throwable $exception): string
    {
        return get_class($exception);
    }
}
