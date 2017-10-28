<?php

namespace TumblrPosts;

use Tumblr\API\Client;

class Tagged
{
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

        return $result;
    }
}
