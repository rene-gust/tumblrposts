<?php

namespace TumblrPosts\Model;

class AbstractItem
{
    public $type;
    public $timestamp;

    /**
     * @param $a
     * @param $b
     * @return bool
     */
    public static function sort($a, $b)
    {
        return $a->timestamp < $b->timestamp;
    }
}
