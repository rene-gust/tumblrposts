<?php

namespace TumblrPosts\Model;

class VideoPlayer
{
    public $width;
    public $height;
    public $embedCode;

    public static function fromResponse(\stdClass $playerFromResponse)
    {
        $player = new static();
        $player->width = $playerFromResponse->width;
        $player->embedCode = $playerFromResponse->embed_code;

        return $player;
    }
}
