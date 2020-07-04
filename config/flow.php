<?php

return [
    'local' => [
        'record' => true,
        'path' => 'flow',
    ],
    'watchers' => [
        EricPridham\Flow\Watchers\RequestWatcher::class,
        EricPridham\Flow\Watchers\LogWatcher::class,
        EricPridham\Flow\Watchers\ExceptionWatcher::class
    ],
    'recorders' => [

    ]
];
