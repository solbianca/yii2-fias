<?php

/**
 * Обновление данных адресов в базе
 */

namespace solbianca\fias\console\models;

use Yii;
use solbianca\fias\console\base\XmlReader;
use yii\helpers\Console;
use solbianca\fias\models\FiasUpdateLog;
use solbianca\fias\models\FiasHouse;
use solbianca\fias\models\FiasAddressObject;

class UpdateModel extends BaseModel
{
    /**
     * Download and unpack fias delta file
     *
     * @param $file
     * @param \solbianca\fias\console\base\Loader $loader
     * @param \solbianca\fias\console\base\SoapResultWrapper $fileInfo
     * @return \solbianca\fias\console\base\Directory
     */
    protected function getDirectory($file, $loader, $fileInfo)
    {
        if (null !== $file) {
            $directory = $loader->wrapDirectory(Yii::getAlias($file));
        } else {
            $directory = $loader->loadUpdateFile($fileInfo);
        }

        return $directory;
    }

    /**
     * @throws \Exception
     */
    public function run()
    {
        return $this->update();
    }

    /**
     * Update fias data in base
     */
    public function update()
    {
        /** @var FiasUpdateLog $currentVersion */
        $currentVersion = FiasUpdateLog::find()->orderBy('id desc')->limit(1)->one();

        if (!$currentVersion) {
            Console::output('База не инициализированна, выполните копанду: php yii fias/install');
            return;
        }

        if (false === $this->loader->isUpdateRequired($currentVersion->version_id)) {
            Console::output('База в актуальном состоянии');
            return;
        }

        Console::output("Вы хотите выполнить обновление с версии {$currentVersion->version_id} на {$this->fileInfo->getVersionId()}");

        $this->deleteFiasData();

        $transaction = Yii::$app->getDb()->beginTransaction();

        try {
            Yii::$app->getDb()->createCommand('SET foreign_key_checks = 0;')->execute();

            $this->updateAddressObject();

            $this->updateHouse();

            $this->saveLog();

            Yii::$app->getDb()->createCommand('SET foreign_key_checks = 1;')->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    private function deleteFiasData()
    {
        Console::output('Удаление данных.');

        $deletedHouseFile = $this->directory->getDeletedHouseFile();
        if ($deletedHouseFile) {
            Console::output("Удаление записей из таблицы " . FiasHouse::tableName() . ".");
            FiasHouse::remove(new XmlReader(
                $deletedHouseFile,
                FiasHouse::XML_OBJECT_KEY,
                array_keys(FiasHouse::getXmlAttributes()),
                FiasHouse::getXmlFilters()
            ));
        }

        $deletedAddressObjectsFile = $this->directory->getDeletedAddressObjectFile();
        if ($deletedAddressObjectsFile) {
            Console::output("Удаление записей из таблицы " . FiasAddressObject::tableName() . ".");
            FiasAddressObject::remove(new XmlReader(
                $deletedAddressObjectsFile,
                FiasAddressObject::XML_OBJECT_KEY,
                array_keys(FiasAddressObject::getXmlAttributes()),
                FiasAddressObject::getXmlFilters()
            ));
        }
    }

    private function updateAddressObject()
    {
        Console::output('Обновление адресов обектов');

        $attributes = FiasAddressObject::getXmlAttributes();
        $attributes['PREVID'] = 'previous_id';

        FiasAddressObject::updateRecords(new XmlReader(
            $this->directory->getAddressObjectFile(),
            FiasAddressObject::XML_OBJECT_KEY,
            array_keys($attributes),
            FiasAddressObject::getXmlFilters()
        ), $attributes);
    }

    private function updateHouse()
    {
        Console::output('Обновление домов');

        $attributes = FiasHouse::getXmlAttributes();
        $attributes['PREVID'] = 'previous_id';

        FiasHouse::updateRecords(new XmlReader(
            $this->directory->getHouseFile(),
            FiasHouse::XML_OBJECT_KEY,
            array_keys($attributes),
            FiasHouse::getXmlFilters()
        ), $attributes);
    }
}