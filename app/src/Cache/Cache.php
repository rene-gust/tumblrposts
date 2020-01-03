<?php

namespace TumblrPosts\Cache;

use Moust\Silex\Cache\CacheInterface;

class Cache
{
    const TTL = 60 * 15;

    protected static $timeKey = 'saved_time';
    protected static $objectKey = 'saved_object';

    /**
     * @var CacheInterface
     */
    protected $moustCache;

    /**
     * seconds
     * @var int
     */
    protected $ttl;

    public function __construct(CacheInterface $cache, $ttl = self::TTL)
    {
        $this->moustCache = $cache;
        $this->ttl = $ttl;
    }

    public function set($key, $value)
    {
        $this->moustCache->store($key, [self::$timeKey => time(), self::$objectKey => $value]);
    }

    public function hasValidCachedObject($key)
    {
        $cachedArray = $this->moustCache->fetch($key);
        $savedTime = 0;
        if (is_array($cachedArray) and array_key_exists(self::$timeKey, $cachedArray)) {
            $savedTime = $cachedArray[self::$timeKey];
        }
        return !empty($savedTime) && time() <= $cachedArray[self::$timeKey] + $this->ttl;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        if ($this->hasValidCachedObject($key)) {
            $predisObject = $this->moustCache->fetch($key);
            return $predisObject[self::$objectKey];
        }
    }

    public function getTaggedKey(string $tags, int $beforeTimestamp)
    {
        return 'tagged_tumblr_posts_' . $tags . '_' . $beforeTimestamp;
    }
}
