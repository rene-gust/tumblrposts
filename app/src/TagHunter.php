<?php

namespace TumblrPosts;

class TagHunter
{
    protected $filePath = __DIR__ . '/../cache/';

    public function saveTags(array $posts, string $groupKey, array $tagPostCounts)
    {
        $cachedTags = $this->readSavedTags($groupKey);

        $receivedTags = [];
        foreach ($posts as $post) {
            foreach ($post->tags as $tag) {
                $receivedTags[$tag] = array_key_exists($tag, $tagPostCounts) ? $tagPostCounts[$tag] : 0;
            }
        }

        foreach ($receivedTags as $tag => $count) {
            if (!array_key_exists($tag, $cachedTags)) {
                $cachedTags[$tag] = $count;
            } else {
                $cachedTags[$tag] = $cachedTags[$tag] + $receivedTags[$tag];
            }
        }

        $this->saveReadTags($groupKey, $cachedTags);
    }

    protected function readSavedTags(string $groupKey)
    {
        if (!is_readable($this->filePath . urlencode($groupKey))) {
            return [];
        }
        $content = file_get_contents($this->filePath . urlencode($groupKey));

        return json_decode($content, true);
    }

    protected function saveReadTags(string $groupKey, array $tags)
    {
        uasort(
            $tags,
            function ($a, $b)
            {
                return $a < $b;
            }
        );

        $content = JSONEncoder::encodeTagCounts($tags);
        file_put_contents($this->filePath . urlencode($groupKey), $content);
    }
}