<?php

namespace EricPridham\Flow\Watchers;

use EricPridham\Flow\Flow;
use EricPridham\Flow\Payloads\ExceptionPayload;
use EricPridham\Flow\Payloads\LogPayload;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;

class LogWatcher
{
    private static $exceptionsSeen;
    public function register(Flow $flow): void
    {
        Event::listen(MessageLogged::class, function (MessageLogged $event) use ($flow) {
            if (!empty($event->context['exception'])) {
                $exception = $event->context['exception'];
                if (self::seenException($exception)) {
                    return;
                }
                self::markExceptionSeen($exception);

                $payload = ExceptionPayload::fromException($exception);
            } else {
                $payload = LogPayload::fromMessageLogged($event);
            }
            $flow->record($payload);
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
