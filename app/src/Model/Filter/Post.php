<?php

namespace TumblrPosts\Model\Filter;

use TumblrPosts\Model\PhotoPost;
use TumblrPosts\Model\TextPost;
use TumblrPosts\Model\VideoPost;

class Post
{
    public $blogger;
    public $timestamp;
    public $caption;
    public $noteCount;
    public $photos;
    public $videos;
    public $text;

    public static function fromPost(\TumblrPosts\Model\Post $fullPost)
    {
        $post = new static();
        $post->blogger = $fullPost->blogger;
        $post->timestamp = $fullPost->timestamp;
        $post->caption = $fullPost->caption;
        $post->noteCount = $fullPost->noteCount;

        if ($fullPost instanceof PhotoPost) {
            $post->photos = $fullPost->photos;
        } elseif ($fullPost instanceof VideoPost) {
            $post->videos = $fullPost->player;
        } elseif ($fullPost instanceof TextPost) {
            $post->text = $fullPost->body;
        }

        static::filterLongLinksCaptionText($post);

        return $post;
    }

    protected static function filterLongLinksCaptionText(Post $post)
    {
        $post->caption = static::filterLongLinks($post->caption);
        $post->text = static::filterLongLinks($post->text);
    }

    protected static function filterLongLinks($text)
    {
        if (preg_match('/(<a[^>]*>)([^<]+)(<\/a>)/', $text, $matches)) {
            if (mb_strlen($matches[2]) > 20) {
                $strippedLinkName = mb_substr($matches[2], 0, 20) . '…';
                $text = preg_replace('/(<a[^>]*>)([^<]+)(<\/a>)/', "$1$strippedLinkName$3", $text);
            }
        }

        return $text;
    }


    /**
     * @param $a
     * @param $b
     * @return bool
     */
    public static function sort(Post $a, Post $b)
    {
        return $a->timestamp < $b->timestamp;
    }
}
