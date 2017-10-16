<?php

namespace TumblrPosts;

use TumblrPosts\Model\TumblrImage;
use TumblrPosts\Model\TumblrVideo;

class BlogPostsResponseParser
{
    /**
     * @return TumblrImage[]
     */
    public static function getTumblrImages($response)
    {
        $images = [];
        foreach ($response->posts as $post) {
            foreach($post->photos as $photo) {
                $image = new TumblrImage();
                $image->url = $photo->original_size->url;
                $image->width = $photo->original_size->width;
                $image->height = $photo->original_size->height;
                $images[] = $image;
            }
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
                $video = new TumblrVideo();
                $video->noteCount = $post->note_count;
                $video->duration = $post->duration;
                $video->format = $post->format;
                $video->videoUrl = $post->video_url;
                $video->thumbnailUrl = $post->thumbnail_url;
                $video->thumbnailWidth = $post->thumbnail_width;
                $video->thumbnailHeight = $post->thumbnail_height;
                $videos[] = $video;
            }
        }
        return $videos;
    }
}
