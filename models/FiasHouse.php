<?php

namespace solbianca\fias\models;

use solbianca\fias\console\traits\DeleteModelTrait;
use solbianca\fias\console\traits\UpdateModelTrait;
use yii\db\ActiveRecord;
use solbianca\fias\console\traits\ImportModelTrait;

/**
 * This is the model class for table "{{%fias_house}}".
 *
 * @property string $id
 * @property string $house_id
 * @property string $address_id
 * @property string $number
 * @property string $building
 * @property string $structure
 * @property string $postal_code
 * @property string $okato
 * @property string $oktmo
 * @property string $ifnsul
 * @property string $ifnsfl
 *
 * @property FiasAddressObject $address
 */
class FiasHouse extends ActiveRecord implements FiasModelInterface
{
    CONST XML_OBJECT_KEY = 'House';

    use ImportModelTrait;
    use UpdateModelTrait;
    use DeleteModelTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%fias_house}}';
    }

    /**
     * @return string
     */
    public static function temporaryTableName()
    {
        return 'tmp_fias_house';
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
    public function rules()
    {
        return [
            [['id', 'house_id'], 'required'],
            [['id', 'house_id', 'address_id'], 'string', 'max' => 36],
            [
                [
                    'number',
                    'full_number',
                    'building',
                    'structure',
                    'postal_code',
                    'okato',
                    'oktmo',
                    'ifnsul',
                    'ifnsfl',
                ],
                'string',
                'max' => 255
            ],
            [
                ['address_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => FiasAddressObject::className(),
                'targetAttribute' => ['address_id' => 'address_id']
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
            'house_id' => 'House ID',
            'address_id' => 'Address ID',
            'number' => 'Number',
            'full_number' => 'Full Number',
            'building' => 'Building',
            'structure' => 'Structure',
            'postal_code' => 'Postal Code',
        ];
    }

    /**
     * @return array
     */
    public static function getXmlAttributes()
    {
        return [
            'HOUSEID' => 'id',
            'HOUSEGUID' => 'house_id',
            'AOGUID' => 'address_id',
            'HOUSENUM' => 'number',
            'BUILDNUM' => 'building',
            'STRUCNUM' => 'structure',
            'POSTALCODE' => 'postal_code',
            'OKATO' => 'okato',
            'OKTMO' => 'oktmo',
            'IFNSUL' => 'ifnsul',
            'IFNSFL' => 'ifnsfl',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(FiasAddressObject::className(), ['address_id' => 'address_id']);
    }

    /**
     * Get full adress for house
     *
     * @return string
     */
    public function getFullAddress()
    {
        $address = (isset($this->address)) ? $this->address->getFullAddress() : $this->number;

        if (!empty($this->building)) {
            $address .= '/' . $this->building;
        }

        if (!empty($this->structure)) {
            $address .= '/' . $this->structure;
        }

        return $address;
    }

    /**
     * Get full house number
     *
     * @return string
     */
    public function getFullNumber()
    {
        $number = $this->number;

        if (!empty($this->building)) {
            $number .= ' корп. ' . $this->building;
        }

        if (!empty($this->structure)) {
            $number .= ' стр. ' . $this->structure;
        }

        return $number;
    }
}
