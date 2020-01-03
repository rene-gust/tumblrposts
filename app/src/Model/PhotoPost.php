<?php

namespace TumblrPosts\Model;

class PhotoPost extends Post
{
    /** @var PostPhoto[] */
    public $photos;


    public static function fromResponse(\stdClass $textPostFromResponse, Post $post = null)
    {
        $photoPost = new static();
        $photoPost = Post::fromResponse($textPostFromResponse, $photoPost);

        if (!empty($textPostFromResponse->photos)) {
            $photos = [];
            foreach ($textPostFromResponse->photos as $photo) {
                $photos[] = PostPhoto::fromResponse($photo);
            }
            $photoPost->photos = $photos;
        }

        return $photoPost;
    }
}
