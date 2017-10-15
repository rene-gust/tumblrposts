<?php

namespace TumblrPosts;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Tumblr\API\Client;

class Controller
{
    /**
     * @param Application $app
     */
    public static function route(Application $app)
    {
        $app->get('/app01/posts', 'TumblrPosts\Controller::posts');
    }

    /**
     * @param Application $app
     * @return Response
     */
    public function posts(Application $app) {
        $client = new Client($app['config']['tumblr_api_consumer_key'], $app['config']['tumblr_api_consumer_secret']);
        $images = Posts::get($app, $client, Posts::TYPE_PHOTO);
        $videos = Posts::get($app, $client, Posts::TYPE_VIDEO);

        return new Response('cool');
    }
}