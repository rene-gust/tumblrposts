<?php

namespace TumblrPosts;

class HexotrisHighScores
{
    /** @var string */
    private $filePath;

    const defaultHighScore = [
        'day' => [
            ['name' => '3DWaffle', 'number' => 1487, 'time' => 1511638940],
            ['name' => 'Chuckles', 'number' => 1387, 'time' => 1511638940],
            ['name' => 'Scooby', 'number' => 1245, 'time' => 1511638940],
            ['name' => 'Capitano', 'number' => 1189, 'time' => 1511638940],
            ['name' => 'Hex Boy', 'number' => 1167, 'time' => 1511638940],
            ['name' => 'Flakes', 'number' => 898, 'time' => 1511638940],
            ['name' => 'Copilot', 'number' => 865, 'time' => 1511638940],
            ['name' => 'Air Hobo', 'number' => 854, 'time' => 1511638940],
            ['name' => 'Risen', 'number' => 834, 'time' => 1511638940],
            ['name' => 'RedFeet', 'number' => 812, 'time' => 1511563999],
        ],
        'all_time' => [
            ['name' => 'OmegaSub', 'number' => 2376, 'time' => 1511638940],
            ['name' => 'AlertXis', 'number' => 2356, 'time' => 1511638940],
            ['name' => 'Potato', 'number' => 2334, 'time' => 1511638940],
            ['name' => 'Doughboy', 'number' => 2323, 'time' => 1511638940],
            ['name' => 'Sapiens', 'number' => 2321, 'time' => 1511638940],
            ['name' => 'HighBomb', 'number' => 2319, 'time' => 1511638940],
            ['name' => 'Hairpin', 'number' => 2298, 'time' => 1511638940],
            ['name' => 'Hemlock', 'number' => 2295, 'time' => 1511638940],
            ['name' => 'Highway', 'number' => 2294, 'time' => 1511638940],
            ['name' => 'Rook', 'number' => 2292, 'time' => 1511638940],
        ],
    ];

    /**
     * @var array
     * @var array
     */
    private $highScores;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        $this->readHighScores();
    }

    /**
     * @return array
     */
    public function getHighScores()
    {
        $todayMidight = new \DateTime(date('Y-m-d 00:00:00'));
        $highScores = $this->highScores;
        foreach ($highScores['day'] as $key => $highScore) {
            if ($highScore['time'] < $todayMidight->getTimestamp() ) {
                unset($highScores['day'][$key]);
            }
        }
        return $highScores;
    }

    /**
     * @param string $name
     * @param int $number
     * @return array
     */
    public function setHighScore($name, $number) {
        $highScores = $this->highScores;
        $highScoreChanged = false;
        $todayMidight = new \DateTime(date('Y-m-d 00:00:00'));
        foreach ($highScores['day'] as $key => $highScore) {
            if ($highScore['number'] < $number || $todayMidight->getTimestamp() > $highScore['time']) {
                $highScores['day'][$key] = ['name' => $name, 'number' => (int)$number, 'time' => time()];
                $highScoreChanged = true;
                break;
            }
        }

        foreach ($highScores['all_time'] as $key => $highScore) {
            if ($highScore['number'] < $number) {
                $highScores['all_time'][$key] = ['name' => $name, 'number' => (int)$number];
                $highScoreChanged = true;
                break;
            }
        }

        if ($highScoreChanged) {
            $this->highScores = $highScores;
            file_put_contents($this->filePath, serialize($highScores));
        }

        return $this->getHighScores();
    }

    /**
     * @return array
     */
    private function readHighScores()
    {
        $fileContent = file_get_contents($this->filePath);
        if (empty($fileContent)) {
            $this->highScores = self::defaultHighScore;
        } else {
            $this->highScores = unserialize($fileContent);
        }
    }
}