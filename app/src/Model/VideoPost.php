<?php

namespace TumblrPosts\Model;

class VideoPost extends Post
{
    public $html5Capable;
    public $thumbnailUrl;
    public $thumbnailWidth;
    public $thumbnailHeight;
    /** @var VideoPlayer */
    public $player;

    public static function fromResponse(\stdClass $videoFromResponse, Post $post = null)
    {
        $video = new static();
        $video = Post::fromResponse($videoFromResponse, $video);
        $video->html5Capable = $videoFromResponse->html5_capable;
        $video->thumbnailUrl = $videoFromResponse->thumbnail_url;
        $video->thumbnailWidth = $videoFromResponse->thumbnail_width;
        $video->thumbnailHeight = $videoFromResponse->thumbnail_height;

        if (!empty($videoFromResponse->player)) {
            $video->player = static::findBiggestPlayer($videoFromResponse);
        }

        return $video;
    }



    protected static function findBiggestPlayer(\stdClass $videoFromResponse)
    {
        $biggestVideoSize = 0;
        $biggestVideoKey  = 0;
        foreach ($videoFromResponse->player as $key => $playerItem) {
            if ($playerItem->width > $biggestVideoSize) {
                $biggestVideoSize = $playerItem->width;
                $biggestVideoKey  = $key;
            }
        }

        return VideoPlayer::fromResponse($videoFromResponse->player[$biggestVideoKey]);
    }
}