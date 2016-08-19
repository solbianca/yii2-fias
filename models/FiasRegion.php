<?php

namespace solbianca\fias\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%fias_region}}".
 *
 * @property string $code
 * @property string $title
 *
 * @property FiasAddressObject[] $fiasAddressObjects
 */
class FiasRegion extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%fias_region}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['code', 'title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code' => 'Code',
            'title' => 'Title',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiasAddressObjects()
    {
        return $this->hasMany(FiasAddressObject::className(), ['region_code' => 'code']);
    }
}
