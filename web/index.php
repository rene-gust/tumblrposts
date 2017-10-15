<?php

require '../vendor/autoload.php';

require '../config.php';

$app = new Silex\Application();

$app['config'] = $config;
$app->register(new Moust\Silex\Provider\CacheServiceProvider(), [
    'caches.options' => [
        'filesystem' => [
            'driver'    => 'file',
            'cache_dir' => '../cache'
        ]
    ]
]);

\TumblrPosts\Controller::route($app);

$app->run();