<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m160714_132152_adress_object_level_table
 */
class m160714_132152_adress_object_level_table extends Migration
{
    public function up()
    {
        Yii::$app->getDb()->createCommand('SET foreign_key_checks = 0;')->execute();

        $this->addColumn('{{%fias_address_object_level}}', 'level',
            $this->integer()->comment('Уровень адресного объекта'));
        $this->addColumn('{{%fias_address_object_level}}', 'short_title',
            $this->string()->comment('Короткое обозначение')->after('title'));

        Yii::$app->getDb()->createCommand('SET foreign_key_checks = 1;')->execute();
    }

    public function down()
    {
        $this->dropColumn('{{%fias_address_object_level}}', 'level');
        $this->dropColumn('{{%fias_address_object_level}}', 'short_title');
    }
}
