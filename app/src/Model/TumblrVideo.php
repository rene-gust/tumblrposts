<?php

namespace TumblrPosts\Model;

use TumblrPosts\Posts;

class TumblrVideo extends AbstractItem
{
    public function __construct() {
        $this->type = Posts::TYPE_VIDEO;
    }

    public $format;
    public $noteCount;
    public $videoUrl;
    public $duration;
    public $thumbnailUrl;
    public $thumbnailWidth;
    public $thumbnailHeight;
    public $playerHtml;
}
