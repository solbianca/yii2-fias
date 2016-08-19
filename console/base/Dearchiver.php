<?php

namespace solbianca\fias\console\base;

use solbianca\fias\helpers\FileHelper;

/**
 * Class Dearchiver
 * @package solbianca\fias\console\base
 *
 * Класс для работы с архивами
 */
class Dearchiver
{
    /**
     * Извлеч файлы из архива
     *
     * @param $pathToFileDirectory
     * @param $pathToFile
     * @return string
     */
    public static function extract($pathToFileDirectory, $pathToFile)
    {
        static::checkPaths($pathToFileDirectory, $pathToFile);
        $directory = static::generateDirectoryName($pathToFileDirectory, $pathToFile);
        static::doExtract($directory, $pathToFile);

        return $directory;
    }

    /**
     * Проверить полученный путь на корректные права
     *
     * @param $pathToFileDirectory
     * @param $pathToFile
     */
    private static function checkPaths($pathToFileDirectory, $pathToFile)
    {
        FileHelper::ensureIsReadable($pathToFile);
        FileHelper::ensureIsDirectory($pathToFileDirectory);
        FileHelper::ensureIsWritable($pathToFileDirectory);
    }

    /**
     * Сгенерировать имя для директории в которую распакуются файлы
     *
     * @param $pathToFileDirectory
     * @param $pathToFile
     * @return string
     */
    private static function generateDirectoryName($pathToFileDirectory, $pathToFile)
    {
        // Формируем имя папки вида VersionID_DateAndTime
        return $pathToFileDirectory
        . '/'
        . explode('_', basename($pathToFile), 1)[0]
        . '_'
        . date('YmdHis');
    }

    /**
     * Извлеч файлы их архива
     *
     * @param $directoryForExtract
     * @param $pathToFile
     * @return string
     * @throws \Exception
     */
    private static function doExtract($directoryForExtract, $pathToFile)
    {
        mkdir($directoryForExtract);

        $pathToFile = escapeshellarg($pathToFile);
        $directoryForExtract = escapeshellarg($directoryForExtract);

        exec('unrar e ' . $pathToFile . ' ' . $directoryForExtract . ' 2>&1', $output, $result);

        if ($result !== 0) {
            throw new \Exception('Ошибка разархивации: ' . implode("\n", $output));
        }

        return $directoryForExtract;
    }
}
