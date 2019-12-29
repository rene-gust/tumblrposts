<?php

namespace TumblrPosts\Model;

class TextPost extends Post
{
    public static function fromResponse(\stdClass $textPostFromResponse, Post $post = null)
    {
        $textPost = new static();
        $textPost = Post::fromResponse($textPostFromResponse, $textPost);

        if (empty($textPostFromResponse->body)) {
            return null;
        }

        if (stripos($textPost->body, '<img') === false) {
            return null;
        }

        return $textPost;
    }
}
