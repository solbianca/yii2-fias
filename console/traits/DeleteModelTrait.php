<?php


namespace solbianca\fias\console\traits;

use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\Console;
use solbianca\fias\console\base\XmlReader;
use solbianca\fias\models\FiasModelInterface;

/**
 * @mixin ActiveRecord
 * @mixin FiasModelInterface
 */
trait DeleteModelTrait
{
    /**
     * @param XmlReader $reader
     */
    public static function remove(XmlReader $reader)
    {
        $count = 0;
        while ($rows = $reader->getRows()) {
            $count += static::removeRows($rows);
            Console::output("Deleted {$count} rows");
        }
    }

    /**
     * @param array $rows
     * @return int
     * @throws InvalidConfigException
     */
    protected static function removeRows(array $rows)
    {
        $ids = [];
        $rowKey = array_search('id', static::getXmlAttributes());
        if ($rowKey === false) {
            throw new InvalidConfigException;
        }
        foreach ($rows as $row) {
            if (!empty($row[$rowKey])) {
                $ids[] = $row[$rowKey];
            }
        }

        return static::deleteAll(['id' => $ids]);
    }
}