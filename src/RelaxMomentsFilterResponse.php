<?php

namespace TumblrPosts;

use TumblrPosts\Model\Post;
use TumblrPosts\Model\Filter\Post as FilteredPost;

class RelaxMomentsFilterResponse
{
    protected static $urls = [];
    
    public static function filterDoubleContent(array $items) {

        static::$urls = [];

        $items = array_filter($items, function(Post $post) {
            if (!in_array($post->url, static::$urls)) {
                static::$urls[] = $post->url;
                return true;
            }
            return false;
        });
        return $items;
    }

    public static function filterRelevantProperties(array $items)
    {
        $filteredItems = [];

        foreach ($items as $item) {
            $filteredItems[] = FilteredPost::fromPost($item);
        }

        return $filteredItems;
    }
}
