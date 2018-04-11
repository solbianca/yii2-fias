<?php

namespace solbianca\fias\models;

use solbianca\fias\console\traits\DeleteModelTrait;
use solbianca\fias\console\traits\UpdateModelTrait;
use yii\db\ActiveRecord;
use solbianca\fias\console\traits\ImportModelTrait;

/**
 * This is the model class for table "{{%fias_address_object}}".
 *
 * @property string $id
 * @property string $address_id
 * @property string $parent_id
 * @property integer $address_level
 * @property string $title
 * @property integer $postal_code
 * @property string $region_code
 * @property string $prefix
 * @property string $area_code
 * @property string $city_code
 * @property string $auto_code
 * @property string $ctar_code
 * @property string $place_code
 * @property string $street_code
 * @property string $extr_code
 * @property string $sext_code
 * @property string $plain_code
 * @property string $code
 * @property string $okato
 * @property string $oktmo
 * @property string $ifnsul
 * @property string $ifnsfl
 *
 * @property FiasAddressObjectLevel $addressLevel
 * @property FiasAddressObject $parent
 * @property FiasAddressObject[] $fiasAddressObjects
 * @property FiasHouse[] $fiasHouses
 */
class FiasAddressObject extends ActiveRecord implements FiasModelInterface
{
    CONST XML_OBJECT_KEY = 'Object';

    use ImportModelTrait;
    use UpdateModelTrait;
    use DeleteModelTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%fias_address_object}}';
    }

    /**
     * @return string
     */
    public static function temporaryTableName()
    {
        return 'tmp_fias_address_object';
    }

    /**
     * @return array
     */
    public static function getXmlAttributes()
    {
        return [
            'AOID' => 'id',
            'AOGUID' => 'address_id',
            'AOLEVEL' => 'address_level',
            'PARENTGUID' => 'parent_id',
            'FORMALNAME' => 'title',
            'POSTALCODE' => 'postal_code',
            'SHORTNAME' => 'prefix',
            'REGIONCODE' => 'region_code',
            'AREACODE' => 'area_code',
            'AUTOCODE' => 'auto_code',
            'CITYCODE' => 'city_code',
            'CTARCODE' => 'ctar_code',
            'PLACECODE' => 'place_code',
            'STREETCODE' => 'street_code',
            'EXTRCODE' => 'extr_code',
            'SEXTCODE' => 'sext_code',
            'PLAINCODE' => 'plain_code',
            'CODE' => 'code',
            'OKATO' => 'okato',
            'OKTMO' => 'oktmo',
            'IFNSUL' => 'ifnsul',
            'IFNSFL' => 'ifnsfl',
        ];
    }

    /**
     * @return array
     */
    public static function getXmlFilters()
    {
        return [['field' => 'ACTSTATUS', 'type' => 'eq', 'value' => 1]];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent_id'], 'required'],
            [['address_level', 'postal_code'], 'integer'],
            [['id', 'address_id', 'parent_id'], 'string', 'max' => 36],
            [
                [
                    'title',
                    'region_code',
                    'prefix',
                    'area_code',
                    'auto_code',
                    'city_code',
                    'ctar_code',
                    'place_code',
                    'street_code',
                    'extr_code',
                    'sext_code',
                    'plain_code',
                    'code',
                    'okato',
                    'oktmo',
                    'ifnsul',
                    'ifnsfl',
                ],
                'string',
                'max' => 255
            ],
            [['address_id'], 'unique'],
            [
                ['address_level'],
                'exist',
                'skipOnError' => true,
                'targetClass' => FiasAddressObjectLevel::className(),
                'targetAttribute' => ['address_level' => 'id']
            ],
            [
                ['parent_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => FiasAddressObject::className(),
                'targetAttribute' => ['parent_id' => 'address_id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address_id' => 'Address ID',
            'parent_id' => 'Parent ID',
            'address_level' => 'Address Level',
            'title' => 'Title',
            'postal_code' => 'Postal Code',
            'region_code' => 'Region',
            'prefix' => 'Prefix',
        ];
    }

    /**
     * @return null|string
     */
    public function getBaseAddressLevel()
    {
        return FiasAddressObjectLevel::getBaseLevel($this->level);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddressLevel()
    {
        return $this->hasOne(FiasAddressObjectLevel::className(),
            ['level' => 'address_level', 'short_title' => 'prefix']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(FiasAddressObject::className(), ['address_id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiasAddressObjects()
    {
        return $this->hasMany(FiasAddressObject::className(), ['parent_id' => 'address_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiasHouses()
    {
        return $this->hasMany(FiasHouse::className(), ['address_id' => 'address_id']);
    }

    /**
     * Get full address for adres objecr
     *
     * @return string
     */
    public function getFullAddress()
    {
        $address = $this->getAddressRecursive();
        $addresses = explode(';', $address);
        $addresses = array_reverse($addresses);
        return implode(', ', $addresses);
    }

    /**
     * @return string
     */
    protected function getAddressRecursive()
    {
        $address = $this->replaceTitle();
        if ($this->parent) {
            $address .= ';' . $this->parent->getAddressRecursive();
        }
        return $address;
    }

    /**
     * Добавить отформатированный префикс к тайтлу
     *
     * @return string
     */
    protected function replaceTitle()
    {
        switch ($this->prefix) {
            case 'обл':
                return $this->title . ' область';
            case 'р-н':
                return $this->title . ' район';
            case 'проезд':
                return $this->title . ' проезд';
            case 'б-р':
                return $this->title . ' бульвар';
            case 'пер':
                return $this->title . ' переулок';
            case 'ал':
                return $this->title . ' аллея';
            case 'ш':
                return $this->title . ' шоссе';
            case 'г':
                return 'г. ' . $this->title;
            case 'линия':
                return 'линия ' . $this->title;
            case 'ул':
                return 'ул. ' . $this->title;
            case 'пр-кт':
                return $this->title . ' проспект';
            default:
                return trim($this->prefix . '. ' . $this->title);
        }
    }
}
