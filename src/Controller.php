<?php

namespace TumblrPosts;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tumblr\API\Client;
use TumblrPosts\Cache\Cache;

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
    public function posts(Application $app)
    {

        $cache = $app['cache'];
        $cache = new Cache($cache);
        $client = new Client($app['config']['tumblr_api_consumer_key'], $app['config']['tumblr_api_consumer_secret']);

        if (!$cache->hasValidCachedObject('images')) {
            $images = Posts::get($app, $client, Posts::TYPE_PHOTO);
            $cache->set('images', json_encode($images));
        } else {
            $images = $cache->get('images');
        }

        if (!$cache->hasValidCachedObject('videos')) {
            $videos = Posts::get($app, $client, Posts::TYPE_VIDEO);
            $cache->set('videos', json_encode($videos));
        } else {
            $videos = $cache->get('videos');
        }

        return new JsonResponse(['images' => $images, 'videos' => $videos]);
    }
}