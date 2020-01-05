<?php

namespace TumblrPosts;

class TagHunter
{
    protected $filePath = __DIR__ . '/../cache/';

    public function saveTags(array $posts, string $groupKey)
    {
        $foundTags = $this->readSavedTags($groupKey);

        foreach ($posts as $post) {
            foreach ($post->tags as $tag) {
                $foundTags[$tag] = $tag;
            }
        }

        $this->saveReadTags($groupKey, $foundTags);
    }

    protected function readSavedTags(string $groupKey)
    {
        if (!is_readable($this->filePath . urlencode($groupKey))) {
            return [];
        }
        $content = file_get_contents($this->filePath . urlencode($groupKey));

        return explode("\n", $content);
    }

    protected function saveReadTags(string $groupKey, array $tags)
    {
        $content = implode("\n", $tags);
        file_put_contents($this->filePath . urlencode($groupKey), $content);
    }
}