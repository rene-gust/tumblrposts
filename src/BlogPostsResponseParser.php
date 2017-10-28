<?php

namespace TumblrPosts;

use TumblrPosts\Model\TumblrPhoto;
use TumblrPosts\Model\TumblrVideo;

class BlogPostsResponseParser
{
    /**
     * @return TumblrPhoto[]
     */
    public static function getTumblrPhotos($response)
    {
        $images = [];
        foreach ($response->posts as $post) {
            $images = array_merge($images, self::getPhotos($post));
        }

        return $images;
    }

    private function getPhotos($post) {
        $images = [];
        foreach ($post->photos as $photo) {
            $image            = new TumblrPhoto();
            $image->url       = $photo->original_size->url;
            $image->width     = $photo->original_size->width;
            $image->height    = $photo->original_size->height;
            $image->timestamp = $post->timestamp;
            $images[]         = $image;
        }
        return $images;
    }

    /**
     * @param $response
     * @return TumblrVideo[]
     */
    public static function getTumblrVideos($response)
    {
        $videos = [];
        foreach ($response->posts as $post) {
            // if empty its probably an instagram video, currently we ignore them
            if (!empty($post->video_url)) {
                $videos[] = self::getVideoFromPost($post);
            }
        }

        return $videos;
    }

    private static function getVideoFromPost($post)
    {
        $video                  = new TumblrVideo();
        $video->noteCount       = $post->note_count;
        $video->duration        = $post->duration;
        $video->format          = $post->format;
        $video->videoUrl        = $post->video_url;
        $video->thumbnailUrl    = $post->thumbnail_url;
        $video->thumbnailWidth  = $post->thumbnail_width;
        $video->thumbnailHeight = $post->thumbnail_height;
        $video->timestamp       = $post->timestamp;
        $video->playerHtml      = $post->player[count($post->player)-1]->embed_code;
        return $video;
    }

    public static function getTagged($response) {
        $items = [];
        foreach ($response as $item) {
            if (!empty($item->photos)) {
                $items = array_merge($items, self::getPhotos($item));
            } elseif (!empty($item->video_url)) {
                $items[] = self::getVideoFromPost($item);
            }
        }

        return $items;
    }
}
