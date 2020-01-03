<?php

namespace TumblrPosts\Model;

class PostPhoto
{
    public $url;
    public $with;
    public $height;

    public static function fromResponse(\stdClass $photoFromResponse)
    {
        $photo = new static();

        if (property_exists($photoFromResponse, 'original_size')) {
            $photo->url = $photoFromResponse->original_size->url;
            $photo->with = $photoFromResponse->original_size->width;
            $photo->height = $photoFromResponse->original_size->height;
        } elseif (property_exists($photoFromResponse, 'alt_sizes')) {
            $photoKey = static::findBiggestAltPhotoKey($photoFromResponse);
            $photo->url = $photoFromResponse->alt_sizes[$photoKey]->url;
            $photo->with = $photoFromResponse->alt_sizes[$photoKey]->width;
            $photo->height = $photoFromResponse->alt_sizes[$photoKey]->height;
        }

        return $photo;
    }

    protected static function findBiggestAltPhotoKey(\stdClass $photo)
    {
        $biggestPhotoSize = 0;
        $biggestPhotoKey = 0;
        foreach ($photo->alt_sizes as $key => $photoItem) {
            if ($photoItem->width > $biggestPhotoSize) {
                $biggestPhotoSize = $photoItem->width;
                $biggestPhotoKey = $key;
            }
        }

        return $biggestPhotoKey;
    }
}
