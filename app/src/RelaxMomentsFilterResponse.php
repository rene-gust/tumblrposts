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

        static::filterInstagramPost($filteredItems);

        $filteredItems = static::filterNonImageNoneVideoPosts($filteredItems);

        static::filterLongWords($filteredItems);

        return $filteredItems;
    }

    protected static function filterLongWords(array $posts)
    {
        foreach ($posts as $post) {
            /** @var FilteredPost $post */
            $post->text = static::filterLongWordsInText($post->text);
        }
    }

    protected static function filterLongWordsInText($text)
    {
        $pureText = preg_replace('/<[^>]+>/', '', $text);
        if (preg_match_all('/(\S{30,})/', $pureText, $matches)) {
            $longWords = $matches[1];
            foreach ($longWords as $longWord) {
                $text = str_replace($longWord, substr($longWord, 0, 29) . '…', $text);
            }
        }

        return $text;
    }

    protected function filterNonImageNoneVideoPosts($posts)
    {
        return array_filter(
            $posts,
            function (FilteredPost $post) {
                return static::hasImageOrVideo($post);
            }
        );
    }

    protected static function hasImageOrVideo(FilteredPost $post)
    {
        if (count($post->photos) > 0) {
            return true;
        }

        if (stripos($post->text, '<img') !== false) {
            return true;
        }

        if ($post->videos && stripos($post->videos->embedCode, '<video') !== false) {
            return true;
        }

        if ($post->videos
            && stripos($post->videos->embedCode, '<iframe') !== false
            && stripos($post->videos->embedCode, 'youtube.com') !== false) {
            return true;
        }

        return false;

    }

    protected static function filterInstagramPost(array $posts)
    {
        foreach ($posts as $post) {
            static::removeInstagramBlockquoteFromPost($post);
        }
    }

    protected static function removeInstagramBlockquoteFromPost(FilteredPost $post)
    {
        $post->text = static::removeInstagramBlockquoteFromText($post->text);

        if ($post->videos) {
            $post->videos->embedCode = static::removeInstagramBlockquoteFromText($post->videos->embedCode);
        }
    }

    protected static function removeInstagramBlockquoteFromText($text)
    {
        if (preg_match('/(<blockquote[^>]*>).*instagram.*(<\/blockquote>)/', $text, $matches)) {
            $text = preg_replace('/(<blockquote[^>]*>).*instagram.*(<\/blockquote>)/', '', $text);
        }

        return $text;
    }
}
