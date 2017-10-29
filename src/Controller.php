<?php

namespace TumblrPosts;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tumblr\API\Client;
use TumblrPosts\Cache\Cache;

class Controller
{
    /**
     * @param Application $app
     */
    public static function route(Application $app)
    {
        $app->get('/app01/posts', 'TumblrPosts\Controller::app01Posts');
        $app->get('/app02/posts/{tags}', 'TumblrPosts\Controller::app02Posts');
    }

    /**
     * @param Application $app
     * @return JsonResponse
     */
    public function app01Posts(Application $app)
    {
        return $this->getPostsResponse(
            $app,
            'app01_items',
            $app['config']['tumblr_api_consumer_key'],
            $app['config']['tumblr_api_consumer_secret'],
            'self::getApp01Items'
        );
    }

    private function getApp01Items(Application $app, $client)
    {
        $items = array_merge(
            [],
            Posts::get($app['config']['app_01']['blogs'], $client, ['type' => Posts::TYPE_PHOTO])
        );
        $items = array_merge(
            $items,
            Posts::get($app['config']['app_01']['blogs'], $client, ['type' => Posts::TYPE_VIDEO])
        );

        uasort($items, '\TumblrPosts\Model\AbstractItem::sort');

        return array_values($items);
    }

    /**
     * @param Application $app
     * @param string      $tags
     * @return JsonResponse
     */
    public function app02Posts(Application $app, $tags)
    {
        return $this->getPostsResponse(
            $app,
            'app02_items_' . $tags,
            $app['config']['tumblr_api_consumer_key'],
            $app['config']['tumblr_api_consumer_secret'],
            'self::getApp02Items',
            explode(',', $tags)
        );
    }

    /**
     * @param Application $app
     * @param Client      $client
     * @param array       $tags
     * @return array
     */
    public function getApp02Items(Application $app, Client $client, $tags)
    {
        $items = array_merge(
            [],
            Tagged::get(
                $tags,
                $client
            )
        );

        return array_values($items);
    }

    /**
     * @param Application $app
     * @param string      $cacheKey
     * @param string      $apiKey
     * @param string      $apiSecret
     * @param array       $tags
     * @return JsonResponse
     */
    private function getPostsResponse(Application $app, $cacheKey, $apiKey, $apiSecret, $getItemsCallable, $tags)
    {
        $cache = new Cache($app['cache']);
        $client = new Client($apiKey, $apiSecret);

        $items = [];
        if (!$cache->hasValidCachedObject($cacheKey)) {
            $items = call_user_func($getItemsCallable, $app, $client, $tags);
            $itemsEncoded = json_encode($items);
            $cache->set($cacheKey, $itemsEncoded);
        } else {
            $itemsEncoded = $cache->get($cacheKey);
        }

        return new JsonResponse(
            $itemsEncoded,
            200,
            [
                'Access-Control-Allow-Origin' => '*',
                'Content-Type' => 'application/json; charset=utf-8',
            ],
            true
        );
    }
}