<?php

namespace TumblrPosts\Model;

class LinkPost extends Post
{
    const YOUTUBE_IFRAME_CODE = '<iframe src="https://www.youtube.com/embed/VIDEO_ID" frameborder="0" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
    /** @var VideoPlayer */
    public $video;

    public static function fromResponse(\stdClass $postFromResponse, Post $post = null)
    {
        $linkPost = new static();
        $linkPost = Post::fromResponse($postFromResponse, $linkPost);

        if (stripos($postFromResponse->url, 'youtube.com') !== false) {
            $videoId = '';
            if (preg_match('/watch\?v=(.*)/', $postFromResponse->url, $matches)) {
                $videoId = $matches[1];
                $video   = new VideoPlayer();
                if ($postFromResponse->link_image_dimensions) {
                    $video->width  = $postFromResponse->link_image_dimensions->width;
                    $video->height = $postFromResponse->link_image_dimensions->height;
                }
                $video->embedCode = str_replace('VIDEO_ID', $videoId, static::YOUTUBE_IFRAME_CODE);
            }

            if (!empty($videoId)) {
                $linkPost->caption = $postFromResponse->description;
                $linkPost->video = $video;
                return $linkPost;
            }
        }

    }
}