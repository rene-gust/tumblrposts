<?php

require '../vendor/autoload.php';

require '../config.php';

$app = new Silex\Application();

$app['config'] = $config;

$app['debug'] = true;

\TumblrPosts\Controller::route($app);

$app->run();