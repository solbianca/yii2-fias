<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m170222_101219_house_id_index
 */
class m170222_101219_house_id_index extends Migration
{
    public function up()
    {
        $this->createIndex('house_address_house_id_key', '{{%fias_house}}', 'house_id');
    }

    public function down()
    {
        $this->dropIndex('house_address_house_id_key', '{{%fias_house}}');
    }
}
