<?php


namespace solbianca\fias\console\traits;

use yii\db\ActiveRecord;
use solbianca\fias\console\base\XmlReader;
use solbianca\fias\models\FiasModelInterface;
use yii\helpers\Console;

/**
 * @mixin ActiveRecord
 * @mixin FiasModelInterface
 */
trait UpdateModelTrait
{
    /**
     * @param XmlReader $reader
     * @param array|null $attributes
     */
    public static function updateRecords(XmlReader $reader, $attributes = null)
    {
        if (is_null($attributes)) {
            $attributes = static::getXmlAttributes();
        }
        static::processUpdateRows($reader, $attributes);
        static::updateCallback();
    }

    public static function processUpdateRows(XmlReader $reader, $attributes)
    {
        $tTableName = static::temporaryTableName();
        $tableName = static::tableName();

        static::getDb()->createCommand("DROP TABLE IF EXISTS {$tTableName};")->execute();
        static::getDb()->createCommand("CREATE TABLE {$tTableName} SELECT * FROM {$tableName} LIMIT 0;")->execute();
        static::getDb()->createCommand()->addColumn($tTableName, 'previous_id', 'char(36)')->execute();

        $count = 0;

        while ($data = $reader->getRows()) {
            $rows = [];
            foreach ($data as $row) {
                $rows[] = array_values($row);
            }
            if ($rows) {
                $count += static::getDb()->createCommand()->batchInsert($tTableName, array_values($attributes),
                    $rows)->execute();
                Console::output("Inserted {$count} rows in tmp table.");
            }
        }

        $count = static::getDb()->createCommand("DELETE old FROM {$tableName} old INNER JOIN {$tTableName} tmp
          ON (old.id = tmp.previous_id OR old.id = tmp.id)")->execute();
        Console::output("Удалено старых записей: {$count}");

        static::getDb()->createCommand()->dropColumn($tTableName, 'previous_id')->execute();
        $count = static::getDb()->createCommand("INSERT INTO {$tableName} SELECT tmp.* FROM {$tTableName} tmp")->execute();
        Console::output("Добавлено новых записей: {$count}");
    }

    /**
     * After update callback
     */
    public static function updateCallback()
    {
        $tTableName = static::temporaryTableName();
        static::getDb()->createCommand()->dropTable($tTableName);
    }
}