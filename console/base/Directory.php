<?php

namespace solbianca\fias\console\base;

use solbianca\fias\helpers\FileHelper;
use yii\console\Exception;

/**
 * Class Directory
 * @package solbianca\fias\console\base
 *
 * Обертка над папкой в которой лежат файлы fias
 */
class Directory
{
    /**
     * Путь до директории
     *
     * @var string
     */
    private $directoryPath;

    /**
     * @param $path
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct($path)
    {
        FileHelper::ensureIsReadable($path);
        FileHelper::ensureIsDirectory($path);

        $this->directoryPath = $path;
    }

    /**
     * Получить id версии базы fias
     *
     * @return mixed
     * @throws Exception
     */
    public function getVersionId()
    {
        $prefix = 'VERSION_ID_';
        return str_replace($prefix, '', $this->find($prefix));
    }

    /**
     * @return null|string
     * @throws Exception
     */
    public function getDeletedAddressObjectFile()
    {
        $fileName = $this->find('AS_DEL_ADDROBJ', false);
        return $fileName ? $this->directoryPath . '/' . $fileName : null;
    }

    /**
     * @return null|string
     * @throws Exception
     */
    public function getDeletedHouseFile()
    {
        $fileName = $this->find('AS_DEL_HOUSE_', false);
        return $fileName ? $this->directoryPath . '/' . $fileName : null;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAddressObjectFile()
    {
        return $this->directoryPath . '/' . $this->find('AS_ADDROBJ');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getHouseFile()
    {
        return $this->directoryPath . '/' . $this->find('AS_HOUSE_');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAddressObjectLevelFile()
    {
        return $this->directoryPath . '/' . $this->find('AS_SOCRBASE');
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->directoryPath;
    }

    /**
     * Найти файл по заданному префиксу
     *
     * @param $prefix
     * @param bool $isIndispensable
     * @return null
     * @throws Exception
     */
    private function find($prefix, $isIndispensable = true)
    {
        $files = scandir($this->directoryPath);
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            if (mb_strpos($file, $prefix) === 0) {
                return $file;
            }
        }

        if ($isIndispensable) {
            throw new Exception('Файл с префиксом ' . $prefix . ' не найден в директории: ' . $this->directoryPath);
        }

        return null;
    }
}
