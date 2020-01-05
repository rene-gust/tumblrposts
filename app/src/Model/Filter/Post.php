<?php

namespace TumblrPosts\Model\Filter;

use TumblrPosts\Model\LinkPost;
use TumblrPosts\Model\PhotoPost;
use TumblrPosts\Model\TextPost;
use TumblrPosts\Model\VideoPlayer;
use TumblrPosts\Model\VideoPost;

class Post
{
    public $blogger;
    public $timestamp;
    public $noteCount;
    public $photos;
    /** @var VideoPlayer */
    public $videos;
    public $text;

    public static function fromPost(\TumblrPosts\Model\Post $fullPost)
    {
        $post            = new static();
        $post->blogger   = $fullPost->blogger;
        $post->timestamp = $fullPost->timestamp;
        $post->text      = $fullPost->caption;
        $post->noteCount = $fullPost->noteCount;

        if ($fullPost instanceof PhotoPost) {
            $post->photos = $fullPost->photos;
        } elseif ($fullPost instanceof VideoPost) {
            $post->videos = $fullPost->player;
        } elseif ($fullPost instanceof TextPost) {
            $post->text = (string)$post->text . $fullPost->body;
        } elseif ($fullPost instanceof LinkPost) {
            $post->videos = $fullPost->video;
        }

        static::filterLongLinksText($post);

        return $post;
    }

    protected static function filterLongLinksText(Post $post)
    {
        $post->text = static::filterLongLinks($post->text);
    }

    protected static function filterLongLinks($text)
    {
        if (preg_match('/(<a[^>]*>)([^<]+)(<\/a>)/', $text, $matches)) {
            if (mb_strlen($matches[2]) > 20) {
                $strippedLinkName = mb_substr($matches[2], 0, 20) . '…';
                $text             = preg_replace('/(<a[^>]*>)([^<]+)(<\/a>)/', "$1$strippedLinkName$3", $text);
            }
        }

        return $text;
    }

    /**
     * @param $a
     * @param $b
     * @return bool
     */
    public static function sort(Post $a, Post $b)
    {
        return $a->timestamp < $b->timestamp;
    }

    public static function toJson(Post $post)
    {
        $post->blogger   = static::removeNonUtf8Chars($post->blogger);
        $post->text   = static::removeNonUtf8Chars($post->text);
        if ($post->videos)  {
            $post->videos->embedCode   = static::removeNonUtf8Chars($post->videos->embedCode);
        }

        return json_encode($post);
    }

    protected static function removeNonUtf8Chars($text)
    {
        $regex = <<<'END'
/
  (
    (?: [\x00-\x7F]               # single-byte sequences   0xxxxxxx
    |   [\xC0-\xDF][\x80-\xBF]    # double-byte sequences   110xxxxx 10xxxxxx
    |   [\xE0-\xEF][\x80-\xBF]{2} # triple-byte sequences   1110xxxx 10xxxxxx * 2
    |   [\xF0-\xF7][\x80-\xBF]{3} # quadruple-byte sequence 11110xxx 10xxxxxx * 3 
    ){1,100}                      # ...one or more times
  )
| ( [\x80-\xBF] )                 # invalid byte in range 10000000 - 10111111
| ( [\xC0-\xFF] )                 # invalid byte in range 11000000 - 11111111
/x
END;

        return preg_replace_callback($regex, "static::utf8replacer", $text);
    }

    protected static function utf8replacer($captures)
    {
        if ($captures[1] != "") {
            // Valid byte sequence. Return unmodified.
            return $captures[1];
        } elseif ($captures[2] != "") {
            // Invalid byte of the form 10xxxxxx.
            // Encode as 11000010 10xxxxxx.
            return "\xC2" . $captures[2];
        } else {
            // Invalid byte of the form 11xxxxxx.
            // Encode as 11000011 10xxxxxx.
            return "\xC3" . chr(ord($captures[3]) - 64);
        }
    }
}
