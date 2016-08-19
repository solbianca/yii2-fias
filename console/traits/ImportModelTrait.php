<?php
namespace solbianca\fias\console\traits;

use solbianca\fias\models\FiasModelInterface;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Console;
use solbianca\fias\console\base\XmlReader;

/**
 * @mixin ActiveRecord
 * @mixin FiasModelInterface
 */
trait ImportModelTrait
{
    protected static $importFile = 'import.csv';

    /**
     * @param XmlReader $reader
     * @param array|null $attributes
     * @throws \yii\db\Exception
     */
    public static function import(XmlReader $reader, $attributes = null)
    {
        if (is_null($attributes)) {
            $attributes = static::getXmlAttributes();
        }
        static::processImportRows($reader, $attributes);
        static::importCallback();
    }


    /**
     * @param XmlReader $reader
     * @param array $attributes
     * @param bool $temporaryTable
     * @throws \yii\db\Exception
     */
    protected static function processImportRows(XmlReader $reader, $attributes)
    {
        $count = 0;
        $tableName = static::tableName();
        $values = implode(', ', array_values($attributes));
        $pathToFile = Yii::$app->getModule('fias')->directory . DIRECTORY_SEPARATOR . static::$importFile;
        $pathToFile = str_replace('\\', '/', $pathToFile);

        while ($data = $reader->getRows()) {
            $rows = [];
            foreach ($data as $row) {
                $rows[] = implode("\t", array_values($row));
            }
            if ($rows) {
                $rows = implode("\n", $rows);
                static::saveInFile($pathToFile, $rows);
                $count += static::getDb()
                    ->createCommand("LOAD DATA LOCAL INFILE '{$pathToFile}' INTO TABLE {$tableName} ({$values})")
                    ->execute();
                Console::output("Inserted {$count} rows");
            }
        }
    }

    protected static function saveInFile($filename, $data)
    {
        return file_put_contents($filename, $data);
    }

    /**
     * After import callback
     */
    public static function importCallback()
    {
    }
}