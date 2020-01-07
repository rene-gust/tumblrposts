<?php

require '../vendor/autoload.php';

require '../config.php';

ob_start("ob_gzhandler");

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
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../twig_views',
));

\TumblrPosts\Controller::route($app);

$app["cors-enabled"]($app);
$app->run();

ob_end_flush();