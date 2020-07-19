<?php

return [
    'enabled' => false,
    'path' => 'flow',
    'watchers' => [
        EricPridham\Flow\Watchers\RequestWatcher::class,
        EricPridham\Flow\Watchers\LogWatcher::class,
        EricPridham\Flow\Watchers\ExceptionWatcher::class,
        EricPridham\Flow\Watchers\ModelWatcher::class,
        EricPridham\Flow\Watchers\QueryWatcher::class,
    ],
    'recorders' => [
        EricPridham\Flow\Recorder\DatabaseRecorder::class,
    ]
];
