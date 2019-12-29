<?php

namespace TumblrPosts\Model;

class Post
{
    const POST_FORMAT_HTML = 'html';
    const POST_FORMAT_MARKDOWN = 'markdown';

    /** @var string[] */
    public $tags;
    public $id;
    public $url;
    public $slug;
    public $date;
    public $timestamp;
    public $state;
    public $format;
    public $reblogKey;
    public $shortUrl;
    public $summary;
    public $body;
    public $shouldOpenInLegacy;
    public $recommendedSource;
    public $recommendedColor;
    public $noteCount;
    public $caption;
    public $canLike;
    public $canReblog;
    public $canSendInMessage;
    public $canReply;
    public $displayAvatar;
    public $blogger;

    public static function fromResponse(\stdClass $postFromResponse, Post $post = null)
    {
        if (is_null($post)) {
            $post = new static();
        }

        $post->blogger = $postFromResponse->blog_name;
        $post->tags = $postFromResponse->tags;
        $post->id = $postFromResponse->id;
        $post->url = $postFromResponse->post_url;
        $post->slug = $postFromResponse->slug;
        $post->date = $postFromResponse->date;
        $post->timestamp = $postFromResponse->timestamp;
        $post->state = $postFromResponse->state;
        $post->format = $postFromResponse->format;
        $post->reblogKey = $postFromResponse->reblog_key;
        $post->shortUrl = $postFromResponse->short_url;
        $post->summary = !empty($postFromResponse->summary) ? $postFromResponse->summary : null;
        $post->body = !empty($postFromResponse->body) ? $postFromResponse->body : null;
        $post->shouldOpenInLegacy = $postFromResponse->should_open_in_legacy;
        $post->recommendedSource = $postFromResponse->recommended_source;
        $post->recommendedColor = $postFromResponse->recommended_color;
        $post->noteCount = $postFromResponse->note_count;
        $post->caption = $postFromResponse->caption;
        $post->canLike = $postFromResponse->can_like;
        $post->canReblog = $postFromResponse->can_reblog;
        $post->canSendInMessage = $postFromResponse->can_send_in_message;
        $post->canReply = $postFromResponse->can_reply;
        $post->displayAvatar = $postFromResponse->display_avatar;

        return $post;
    }

}