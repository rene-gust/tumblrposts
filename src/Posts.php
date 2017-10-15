<?php

namespace TumblrPosts;

use Silex\Application;
use Tumblr\API\Client;

class Posts
{
    const TYPE_PHOTO = 'photo';
    const TYPE_VIDEO = 'video';

    /**
     * @param Application $app
     * @param Client      $client
     * @return array
     */
    public static function get(Application $app, Client $client, $type = self::TYPE_PHOTO)
    {
        $result = [];
        for($i = 0; $i < 10; ++$i) {
            $result = array_merge($result, self::getItems($client, $app, $type, $i));
        }
        return $result;
    }

    /**
     * @param Client      $client
     * @param Application $app
     * @param string      $type
     * @param string      $offset
     * @return Model\TumblrImage[]|Model\TumblrVideo[]
     */
    private static function getItems(Client $client, Application $app, $type, $offset)
    {
        if (self::TYPE_PHOTO == $type) {
            return BlogPostsResponseParser::getTumblrImages(
                $client->getBlogPosts(
                    $app['config']['app_01']['blog_name'],
                    [
                        'type' => $type,
                        'offset' => $offset
                    ]
                )
            );
        } elseif (self::TYPE_VIDEO == $type) {
            return BlogPostsResponseParser::getTumblrVideos(
                $client->getBlogPosts(
                    $app['config']['app_01']['blog_name'],
                    [
                        'type' => $type,
                        'offset' => $offset
                    ]
                )
            );
        }
    }
}
