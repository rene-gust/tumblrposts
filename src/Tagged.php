<?php

namespace TumblrPosts;

use Tumblr\API\Client as TumblerClient;
use TumblrPosts\Cache\Cache;

class Tagged
{
    /**
     * @param array $tags
     * @param       $consumerKey
     * @param       $consumerSecret
     * @param       $beforeTimestamp
     * @param Cache $cache
     * @return array
     */
    public static function get(array $tags, $consumerKey, $consumerSecret, $beforeTimestamp, Cache $cache)
    {
        $client = new TumblerClient($consumerKey, $consumerSecret);

        $result = [];

        if ($beforeTimestamp == 0) {
            $beforeTimestamp = time();
        }

        foreach ($tags as $tag) {

            $tag = urlencode($tag);
            $response = $client->getTaggedPosts($tag, ['before' => $beforeTimestamp]);
            if (!empty($response)) {
                $result = array_merge($result, BlogPostsResponseParser::getTagged($response));
            }
        }

        $result = RelaxMomentsFilterResponse::filterDoubleContent($result);
        $result = RelaxMomentsFilterResponse::filterRelevantProperties($result);

        uasort($result, '\TumblrPosts\Model\Filter\Post::sort');

        return $result;
    }
}
