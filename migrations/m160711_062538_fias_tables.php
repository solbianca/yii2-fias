<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m160711_062538_fias_tables
 */
class m160711_062538_fias_tables extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%fias_house}}', [
            'id' => $this->char(36)->notNull()->comment('Идентификационный код записи'),
            'house_id' => $this->char(36)->notNull()->comment('Идентификационный код дома'),
            'address_id' => $this->char(36)->comment('Идентификационный код адресного объекта'),
            'number' => $this->string()->comment('Номер дома'),
            'building' => $this->string()->comment('Корпус'),
            'structure' => $this->string()->comment('Строение'),
            'postal_code' => $this->string()->comment('Индекс'),
            'oktmo' => $this->string()->comment('Код по справочнику ОКТМО'),
            'okato' => $this->string()->comment('Код по справочнику ОКАТО'),
            'ifnsul' => $this->integer()->comment('Код ИФНС ЮЛ'),
            'ifnsfl' => $this->integer()->comment('Код ИФНС ФЛ')
        ], $tableOptions);

        $this->addPrimaryKey('pk', '{{%fias_house}}', 'id');
        $this->createIndex('house_address_id_fkey_idx', '{{%fias_house}}', 'address_id');

        $this->createTable('{{%fias_address_object}}', [
            'id' => $this->char(36)->notNull()->comment('Идентификационный код записи'),
            'address_id' => $this->char(36)->unique()->comment('Идентификационный код адресного объекта'),
            'parent_id' => $this->char(36)->notNull()->comment('Идентификационный код родительского адресного объекта'),
            'address_level' => $this->integer()->comment('Уровень объекта по ФИАС'),
            'title' => $this->string()->comment('Наименование объекта'),
            'postal_code' => $this->integer()->comment('Почтовый индекс'),
            'region' => $this->string()->comment('Регион'),
            'prefix' => $this->string()->comment('Ул., пр. и так далее'),
            'area_code' => $this->string()->comment('Код района'),
            'auto_code' => $this->string()->comment('Код автономии'),
            'city_code' => $this->string()->comment('Код города'),
            'ctar_code' => $this->string()->comment('Код внутригородского района'),
            'place_code' => $this->string()->comment('Код населённого пункта'),
            'street_code' => $this->string()->comment('Код улицы'),
            'extr_code' => $this->string()->comment('Код дополнительного адресообразующего элемента'),
            'sext_code' => $this->string()->comment('Код подчиненного дополнительного адресообразующего элемента'),
            'plain_code' => $this->string()->comment('Код адресного объекта из КЛАДР 4.0 одной строкой без признака актуальности (последних двух '),
            'code' => $this->string()->comment('Код адресного объекта одной строкой с признаком актуальности из КЛАДР 4.0'),
            'okato' => $this->string()->comment('Код по справочнику ОКАТО'),
            'oktmo' => $this->string()->comment('Код по справочнику ОКТМО'),
            'ifnsul' => $this->integer()->comment('Код ИФНС ЮЛ'),
            'ifnsfl' => $this->integer()->comment('Код ИФНС ФЛ')
        ], $tableOptions);

        $this->addPrimaryKey('pk', '{{%fias_address_object}}', 'id');
        $this->createIndex('address_object_parent_id_fkey_idx', '{{%fias_address_object}}', 'parent_id');;
        $this->createIndex('address_object_title_lower_idx', '{{%fias_address_object}}', 'title');

        $this->createTable('{{%fias_address_object_level}}', [
            'title' => $this->string()->comment('Описание уровня'),
            'code' => $this->string()->comment('Код уровня'),
        ], $tableOptions);

        $this->addPrimaryKey('pk', '{{%fias_address_object_level}}', ['title', 'code']);

        $this->createTable('{{%fias_update_log}}', [
            'id' => $this->primaryKey(),
            'version_id' => $this->integer()->unique()->notNull()->comment('ID версии, полученной от базы ФИАС'),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%fias_region}}', [
            'id' => $this->string()->comment('Номер региона'),
            'title' => $this->string()->comment('Название региона'),
        ], $tableOptions);

        $this->addPrimaryKey('pk', '{{%fias_region}}', 'id');

        $this->addForeignKey('houses_parent_id_fkey', '{{%fias_house}}', 'address_id', '{{%fias_address_object}}',
            'address_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('address_object_parent_id_fkey', '{{%fias_address_object}}', 'parent_id',
            '{{%fias_address_object}}', 'address_id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%fias_house}}');
        $this->dropTable('{{%fias_address_object}}');
        $this->dropTable('{{%fias_address_object_level}}');
        $this->dropTable('{{%fias_update_log}}');
        $this->dropTable('{{%fias_region}}');
    }
}
