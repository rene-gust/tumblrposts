<?php

namespace TumblrPosts\Model;

class VideoPlayer
{
    public $with;
    public $embedCode;

    public static function fromResponse(\stdClass $playerFromResponse)
    {
        $player = new static();
        $player->with = $playerFromResponse->width;
        $player->embedCode = $playerFromResponse->embed_code;

        return $player;
    }
}
