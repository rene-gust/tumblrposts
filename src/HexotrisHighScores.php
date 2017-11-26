<?php

namespace TumblrPosts;

class HexotrisHighScores
{
    /** @var string */
    private $filePath;

    const defaultHighScore = [
        'day' => [
            ['name' => '3DWaffle', 'number' => 3487, 'time' => 1511638940],
            ['name' => 'Chuckles', 'number' => 3387, 'time' => 1511638940],
            ['name' => 'Scooby', 'number' => 3245, 'time' => 1511638940],
            ['name' => 'Capitano', 'number' => 3189, 'time' => 1511638940],
            ['name' => 'Hex Boy', 'number' => 3167, 'time' => 1511638940],
            ['name' => 'Flakes', 'number' => 2898, 'time' => 1511638940],
            ['name' => 'Copilot', 'number' => 2865, 'time' => 1511638940],
            ['name' => 'Air Hobo', 'number' => 2854, 'time' => 1511638940],
            ['name' => 'Risen', 'number' => 2834, 'time' => 1511638940],
            ['name' => 'RedFeet', 'number' => 2812, 'time' => 1511563999],
        ],
        'all_time' => [
            ['name' => 'OmegaSub', 'number' => 4376, 'time' => 1511638940],
            ['name' => 'AlertXis', 'number' => 4356, 'time' => 1511638940],
            ['name' => 'Potato', 'number' => 4334, 'time' => 1511638940],
            ['name' => 'Doughboy', 'number' => 4323, 'time' => 1511638940],
            ['name' => 'Sapiens', 'number' => 4321, 'time' => 1511638940],
            ['name' => 'HighBomb', 'number' => 4319, 'time' => 1511638940],
            ['name' => 'Hairpin', 'number' => 4298, 'time' => 1511638940],
            ['name' => 'Hemlock', 'number' => 4295, 'time' => 1511638940],
            ['name' => 'Highway', 'number' => 4294, 'time' => 1511638940],
            ['name' => 'Rook', 'number' => 4292, 'time' => 1511638940],
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
        $highScores = $this->readHighScores();
        $highScoreChanged = false;
        foreach ($highScores['day'] as $key => $highScore) {
            if ($highScore['number'] < $number) {
                $highScores['day'][$key] = ['name' => $name, 'number' => (int)$number];
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