<?php

namespace TumblrPosts;

use TumblrPosts\Model\Filter\Post;

class JSONEncoder
{
    public static function encode(array $posts)
    {
        $encodedposts = [];

        foreach ($posts as $post) {
            $encodedposts[] = Post::toJson($post);
        }

        return '[' . implode(',', $encodedposts) . ']';
    }
}
