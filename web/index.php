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
$app->register(new JDesrosiers\Silex\Provider\CorsServiceProvider(), [
    "cors.allowOrigin" => "*",
]);

\TumblrPosts\Controller::route($app);

$app["cors-enabled"]($app);
$app->run();