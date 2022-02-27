<?php

return [
    'enabled' => env('FLOW_ENABLED', false),
    'watchers' => [
        EricPridham\Flow\Watchers\RequestWatcher::class,
        EricPridham\Flow\Watchers\LogWatcher::class,
        EricPridham\Flow\Watchers\ExceptionWatcher::class,
        EricPridham\Flow\Watchers\ModelWatcher::class,
        EricPridham\Flow\Watchers\QueryWatcher::class,
        EricPridham\Flow\Watchers\CommandWatcher::class,
    ],
    'recorders' => [
        EricPridham\Flow\Recorder\DatabaseRecorder::class => [
            'path' => env('FLOW_PATH', 'flow'),
            'middleware' => [],
        ],
    ]
];
