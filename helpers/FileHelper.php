<?php

namespace solbianca\fias\helpers;

use yii\base\InvalidConfigException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;

/**
 * Class FileHelper
 * @package solbianca\fias\helpers
 */
class FileHelper
{
    /**
     * @param $path
     * @throws InvalidConfigException
     */
    public static function ensureIsReadable($path)
    {
        if (!is_readable($path)) {
            throw new InvalidConfigException('Путь недоступен для чтения: ' . $path);
        }
    }

    /**
     * @param $path
     * @throws InvalidConfigException
     */
    public static function ensureIsWritable($path)
    {
        if (!is_writable($path)) {
            throw new InvalidConfigException('Путь недоступен для записи: ' . $path);
        }
    }

    /**
     * @param $path
     * @return bool
     */
    public static function ensureIsDirectory($path)
    {
        return is_dir($path) || mkdir($path);
    }

    /**
     * @param $path
     * @return bool
     */
    public static function clearDirectory($path)
    {
        $di = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
        $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($ri as $file) {
            $file->isDir() ? rmdir($file) : unlink($file);
        }
        return true;
    }
}
