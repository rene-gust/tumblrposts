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

        return $post;
    }
}
