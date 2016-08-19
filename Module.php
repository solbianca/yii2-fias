<?php

namespace solbianca\fias;

use Yii;

/**
 * Class Module
 * @package solbianca\fias
 *
 * @property string $directory
 */
class Module extends \yii\base\Module
{
    /**
     * Directory for fias files
     *
     * @var string
     */
    private $directory;

    /**
     * @inherit
     */
    public function init()
    {
        parent::init();

        $this->directory = Yii::getAlias('@app/runtime/fias');
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @param string $value
     */
    public function setDirectory($value)
    {
        $this->directory = $value;
    }
}
