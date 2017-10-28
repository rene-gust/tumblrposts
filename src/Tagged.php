<?php

namespace TumblrPosts;

use Tumblr\API\Client;
use TumblrPosts\Model\TumblrPhoto;
use TumblrPosts\Model\TumblrVideo;

class Tagged
{
    private static $urls = [];
    /**
     * @param array  $tags
     * @param Client $client
     * @return array
     */
    public static function get(array $tags, Client $client, $options)
    {
        $result = [];
        foreach ($tags as $tag) {
            for ($i = 0; $i < $options['offset_max']; ++$i) {
                $options['offset'] = $i;
                $result = array_merge($result, BlogPostsResponseParser::getTagged($client->getTaggedPosts($tag)));
            }
        }

        $result = self::filterDoubleContent($result);

        uasort($result, '\TumblrPosts\Model\AbstractItem::sort');

        return $result;
    }

    private static function filterDoubleContent($items) {
        $items = array_filter($items, function($item) {
            $url = '';
            if ($item instanceof TumblrPhoto) {
                $url = $item->url;
            } elseif ($item instanceof TumblrVideo) {
                $url = $item->videoUrl;
            }

            if (!in_array($url, self::$urls)) {
                self::$urls[] = $url;
                return true;
            }
            return false;
        });
        return $items;
    }
}
