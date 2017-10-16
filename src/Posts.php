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
        $offsetMaxKey = $type == self::TYPE_PHOTO ? 'images_offset_max' : 'videos_offset_max';
        foreach ($app['config']['app_01']['blogs'] as $blog) {
            for ($i = 0; $i < $blog[$offsetMaxKey]; ++$i) {
                $result = array_merge($result, self::getItems($client, $type, $blog['name'], $i));
            }
        }

        return $result;
    }

    /**
     * @param Client $client
     * @param string $type
     * @param string $blogName
     * @param string $offset
     * @return Model\TumblrImage[]|Model\TumblrVideo[]
     */
    private static function getItems(Client $client, $type, $blogName, $offset)
    {
        if (self::TYPE_PHOTO == $type) {
            return BlogPostsResponseParser::getTumblrImages(
                $client->getBlogPosts(
                    $blogName,
                    [
                        'type'   => $type,
                        'offset' => $offset,
                    ]
                )
            );
        } elseif (self::TYPE_VIDEO == $type) {
            return BlogPostsResponseParser::getTumblrVideos(
                $client->getBlogPosts(
                    $blogName,
                    [
                        'type'   => $type,
                        'offset' => $offset,
                    ]
                )
            );
        }
    }
}
