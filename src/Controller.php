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

        $items = [];
        if (!$cache->hasValidCachedObject('app01_items')) {
            $items = array_merge($items, Posts::get($app, $client, Posts::TYPE_PHOTO));
            $items = array_merge($items, Posts::get($app, $client, Posts::TYPE_VIDEO));

            uasort($items, '\TumblrPosts\Model\AbstractItem::sort');
            $items = array_values($items);

            $itemsEncoded = json_encode($items);
            $cache->set('app01_items', $itemsEncoded);
        } else {
            $itemsEncoded = $cache->get('app01_items');
        }

        return new JsonResponse(
            $itemsEncoded,
            200,
            [
                'Access-Control-Allow-Origin' => '*',
                'Content-Type' => 'application/json; charset=utf-8'
            ],
            true
        );
    }
}