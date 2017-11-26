<?php

namespace TumblrPosts;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        $app->post('/app03', 'TumblrPosts\Controller::app03Url');
        $app->post('/hexotris', 'TumblrPosts\Controller::hexotrisPost');
        $app->get('/hexotris', 'TumblrPosts\Controller::hexotrisGet');
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

    /**
     * @param Application $app
     * @param Request $request
     * @return Response
     */
    public function app03Url(Application $app, Request $request) {
        $data = json_decode($request->getContent(), true);
        $url = $data['url'] ?? '';
        if (empty($url)) {
            throw new NotFoundHttpException();
        }
        $cache = new Cache($app['cache']);
        if (!$cache->hasValidCachedObject($url)) {
            $content = file_get_contents($url);
            $cache->set($url, $content);
        } else {
            $content = $cache->get($url);
        }

        return new Response(
            $content,
            200,
            ['Access-Control-Allow-Origin' => '*']
        );
    }

    /**
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     */
    public function hexotrisPost(Application $app, Request $request) {
        $service = new HexotrisHighScores($app['config']['hexotris']['file']);
        $highScores = $service->setHighScore(
            $request->request->get('highScoreName', 'anonymous'),
            $request->request->get('highScoreNumber', 0)
        );

        return new JsonResponse($highScores);
    }

    /**
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     */
    public function hexotrisGet(Application $app, Request $request) {
        return new JsonResponse((new HexotrisHighScores($app['config']['hexotris']['file']))->getHighScores());
    }
}