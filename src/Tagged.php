<?php

namespace TumblrPosts;

use Tumblr\API\Client as TumblerClient;

class Tagged
{
    private static $urls = [];
    /**
     * @param array  $tags
     * @param string $apiKey
     * @return array
     */
    public static function get(array $tags, $consumerKey, $consumerSecret)
    {
        $client = new TumblerClient($consumerKey, $consumerSecret);

        $result = [];
        foreach ($tags as $tag) {
            $tag = urlencode($tag);
            $response = $client->getTaggedPosts($tag);
            if (!empty($response)) {
                $result = array_merge($result, BlogPostsResponseParser::getTagged($response));
            }
        }

        $result = RelaxMomentsFilterResponse::filterDoubleContent($result);
        $result = RelaxMomentsFilterResponse::filterRelevantProperties($result);

        uasort($result, '\TumblrPosts\Model\AbstractItem::sort');

        return $result;
    }
}
