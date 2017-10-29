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
     * @param string $apiKey
     * @return array
     */
    public static function get(array $tags, $apiKey)
    {
        $result = [];
        foreach ($tags as $tag) {
            $tag = urlencode($tag);
            $responseJson = file_get_contents("https://api.tumblr.com/v2/tagged?tag=$tag&api_key=$apiKey");
            if ($responseJson === false) {
                return $result;
            }
            $responseObject = json_decode($responseJson);
            if (empty($responseObject->response)) {
                return $result;
            }
            $result = array_merge($result, BlogPostsResponseParser::getTagged($responseObject->response));
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
