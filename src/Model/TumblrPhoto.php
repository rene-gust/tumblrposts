<?php

namespace TumblrPosts\Model;

use TumblrPosts\Posts;

class TumblrPhoto extends AbstractItem
{
    public function __construct() {
        $this->type = Posts::TYPE_PHOTO;
    }

    public $url;
    public $width;
    public $height;
}
