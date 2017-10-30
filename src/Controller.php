<?php

namespace TumblrPosts;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Tumblr\API\Client;
use TumblrPosts\Cache\Cache;

class Controller
{
    /**
     * @param Application $app
     */
    public static function route(Application $app)
    {
        $app->post('/app01/posts', 'TumblrPosts\Controller::app01Posts');
        $app->get('/app02/posts/{tags}', 'TumblrPosts\Controller::app02Posts');
    }

    /**
     * @param Application $app
     * @param Request     $request
     * @return JsonResponse
     */
    public function app01Posts(Application $app, Request $request)
    {
        $blogs = json_decode($request->getContent(), true);
        return $this->getPostsResponse(
            $app,
            'app01_items',
            'self::getApp01Items',
            $blogs
        );
    }

    private function getApp01Items(Application $app, $blogs)
    {
        $client = new Client($app['config']['tumblr_api_consumer_key'], $app['config']['tumblr_api_consumer_secret']);

        $items = array_merge(
            [],
            Posts::get($blogs, $client, ['type' => Posts::TYPE_PHOTO])
        );
        $items = array_merge(
            $items,
            Posts::get($blogs, $client, ['type' => Posts::TYPE_VIDEO])
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
            'self::getApp02Items',
            explode(',', $tags)
        );
    }

    /**
     * @param Application $app
     * @param array       $tags
     * @return array
     */
    public function getApp02Items(Application $app, $tags)
    {
        $items = array_merge(
            [],
            Tagged::get(
                $tags,
                $app['config']['app_02']['tumblr_api_consumer_key']
            )
        );

        return array_values($items);
    }

    /**
     * @param Application $app
     * @param string      $cacheKey
     * @param array       $tags
     * @return JsonResponse
     */
    private function getPostsResponse(Application $app, $cacheKey, $getItemsCallable, $tags)
    {
        $cache = new Cache($app['cache']);

        $items = [];
        if (!$cache->hasValidCachedObject($cacheKey)) {
            $items = call_user_func($getItemsCallable, $app, $tags);
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