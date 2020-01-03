<?php

namespace TumblrPosts;

use TumblrPosts\Model\PhotoPost;
use TumblrPosts\Model\Post;
use TumblrPosts\Model\TextPost;
use TumblrPosts\Model\TumblrPhoto;
use TumblrPosts\Model\TumblrVideo;
use TumblrPosts\Model\VideoPost;

class BlogPostsResponseParser
{
    const POST_TYPE_TEXT   = 'text';
    const POST_TYPE_QUOTE  = 'quote';
    const POST_TYPE_ANSWER = 'answer';
    const POST_TYPE_VIDEO  = 'video';
    const POST_TYPE_AUDIO  = 'audio';
    const POST_TYPE_PHOTO  = 'photo';
    const POST_TYPE_CHAT   = 'chat';

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

    private function getPhotos($post)
    {
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
        $video->playerHtml      = $post->player[count($post->player) - 1]->embed_code;
        return $video;
    }

    public static function getTagged($response)
    {
        $parsedItems = [];
        foreach ($response as $item) {

            if ($item->type == static::POST_TYPE_VIDEO) {
                $parsedItem = VideoPost::fromResponse($item);
            } elseif ($item->type == static::POST_TYPE_PHOTO) {
                $parsedItem = PhotoPost::fromResponse($item);
            } elseif ($item->type == static::POST_TYPE_TEXT) {
                $parsedItem = TextPost::fromResponse($item);
            }

            if ($parsedItem instanceof Post) {
                $parsedItems[] = $parsedItem;
            }
        }

        return $parsedItems;
    }
}
