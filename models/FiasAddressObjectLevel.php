<?php

namespace solbianca\fias\models;

use Yii;
use solbianca\fias\console\traits\ImportModelTrait;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%fias_address_object_level}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $short_title
 * @property string $code
 * @property integer $level
 *
 * @property FiasAddressObject[] $fiasAddressObjects
 */
class FiasAddressObjectLevel extends ActiveRecord implements FiasModelInterface
{
    use ImportModelTrait;

    CONST XML_OBJECT_KEY = 'AddressObjectType';

    private static $baseLevels = [
        0 => 'Не определен',
        1 => 'Регион',
        2 => 'Зарезервирован',
        3 => 'Район',
        4 => 'Город',
        5 => 'Внутригородская территория',
        6 => 'Населенный пункт',
        7 => 'Улица',
        8 => 'Зарезервирован',
        90 => 'Дополнительная территория (ГСК, СНТ, лагери отдыха и т.п.)',
        91 => 'Улицы на дополнительной территории (улицы, линии, проезды)',
    ];

    /**
     * @return array
     */
    public static function getXmlAttributes()
    {
        return [
            'LEVEL' => 'level',
            'SOCRNAME' => 'title',
            'KOD_T_ST' => 'code',
            'SCNAME' => 'short_title'
        ];
    }

    /**
     * @return array
     */
    public static function getXmlFilters()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%fias_address_object_level}}';
    }

    /**
     * @inheritdoc
     */
    public static function temporaryTableName()
    {
        return 'tmp_fias_address_object_level';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'level'], 'integer'],
            [['title', 'code', 'short_title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'short_title' => 'Short Title',
            'code' => 'Code',
            'level' => 'Level'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiasAddressObjects()
    {
        return $this->hasMany(FiasAddressObject::className(), ['address_level' => 'id']);
    }

    /**
     * @return array
     */
    public static function getBaseLevels()
    {
        return self::$baseLevels;
    }

    /**
     * @param integer $level
     * @return string|null
     */
    public static function getBaseLevel($level)
    {
        if (isset(self::$baseLevels[$level])) {
            return self::$baseLevels[$level];
        }

        return null;
    }
}
