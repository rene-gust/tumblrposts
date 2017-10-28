<?php

namespace TumblrPosts;

use Silex\Application;
use Tumblr\API\Client;

class Posts
{
    const TYPE_PHOTO = 'photo';
    const TYPE_VIDEO = 'video';

    /**
     * @param array  $blogs
     * @param Client $client
     * @param array  $options
     * @return array
     */
    public static function get(array $blogs, Client $client, $options = [])
    {
        $result = [];
        $type = self::TYPE_PHOTO;
        if (array_key_exists('type', $options)) {
            $type = $options['type'];
        } else {
            $options['type'] = $type;
        }
        $offsetMaxKey = ($type == self::TYPE_PHOTO) ? 'images_offset_max' : 'videos_offset_max';
        foreach ($blogs as $blog) {
            for ($i = 0; $i < $blog[$offsetMaxKey]; ++$i) {
                $options['offset'] = $i;
                $result = array_merge($result, self::getItems($client, $type, $blog['name'], $options));
            }
        }

        return $result;
    }

    /**
     * @param Client $client
     * @param string $type
     * @param string $blogName
     * @param array  $options
     * @return Model\TumblrPhoto[]|Model\TumblrVideo[]
     */
    private static function getItems(Client $client, $type, $blogName, $options)
    {
        if (self::TYPE_PHOTO == $type) {
            return BlogPostsResponseParser::getTumblrPhotos($client->getBlogPosts($blogName, $options));
        } elseif (self::TYPE_VIDEO == $type) {
            return BlogPostsResponseParser::getTumblrVideos($client->getBlogPosts($blogName, $options));
        }
    }
}
