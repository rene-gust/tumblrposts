<?php

namespace TumblrPosts;

use Tumblr\API\Client as TumblerClient;

class Tagged
{
    /**
     * @param array $tags
     * @param       $consumerKey
     * @param       $consumerSecret
     * @param       $beforeTimestamp
     * @return array
     */
    public static function get(array $tags, $consumerKey, $consumerSecret, $beforeTimestamp)
    {
        $client = new TumblerClient($consumerKey, $consumerSecret);

        $posts = [];
        $tagPostCounts = [];

        if ($beforeTimestamp == 0) {
            $beforeTimestamp = time();
        }

        foreach ($tags as $tag) {

            $tag = urlencode($tag);
            $response = $client->getTaggedPosts($tag, ['before' => $beforeTimestamp]);
            if (!empty($response)) {
                $postsForCurrentTag = BlogPostsResponseParser::getTagged($response);
                $posts              = array_merge($posts, $postsForCurrentTag);
                $tagPostCounts[$tag] = count($postsForCurrentTag);
            }
        }

        $tagHunter = new TagHunter();
        $tagHunter->saveTags($posts, implode('_', $tags), $tagPostCounts);

        $posts = RelaxMomentsFilterResponse::filterDoubleContent($posts);
        $posts = RelaxMomentsFilterResponse::filterRelevantProperties($posts);

        uasort($posts, '\TumblrPosts\Model\Filter\Post::sort');

        return $posts;
    }
}
