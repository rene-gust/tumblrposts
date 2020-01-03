<?php

namespace TumblrPosts\Model;

class Blog
{
    public $name;
    public $title;
    public $url;
    public $uuid;
    public $updated;

    public static function fromAPIResponse(\stdClass $blogAPIResponse) {
        $blog = new static();
        $blog->name = $blogAPIResponse->name;
        $blog->title = $blogAPIResponse->title;
        $blog->url = $blogAPIResponse->url;
        $blog->uuid = $blogAPIResponse->uuid;
        $blog->updated = $blogAPIResponse->updated;
    }
}