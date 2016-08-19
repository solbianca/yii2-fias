<?php

namespace solbianca\fias\searches;

use solbianca\fias\models\FiasAddressObject;
use solbianca\fias\models\FiasHouse;
use yii\base\Model;
use Yii;
use yii\data\ActiveDataProvider;

class SearchAddress extends Model
{
    /**
     * @var int
     */
    public $region;

    /**
     * @var string
     */
    public $street;

    /**
     * @var string
     */
    public $address_id;

    /**
     * @var string
     */
    public $house;

    /**
     * Поиск по улицам и улицам на дополнительных территориях
     *
     * @var array
     */
    protected $levels = [7, 91];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['address_id', 'house', 'street'], 'string'],
            [['region'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @param $params
     * @return array
     */
    public function searchAddress($params)
    {
        if (!empty($params['house'])) {
            $dataProvider = $this->searchHouse($params);
            $models = $dataProvider->getModels();
            if (empty($models)) {
                return ['result' => true, 'data' => null];
            }

            foreach ($models as $model) {
                $data[] = $model->getFullNumber();
            }
            return ['result' => true, 'data' => $data];
        } elseif (!empty($params['street'])) {
            $dataProvider = $this->searchAddressObject($params);
            $models = $dataProvider->getModels();
            if (empty($models)) {
                return ['result' => true, 'data' => null];
            }

            foreach ($models as $model) {
                $data[] = [
                    'value' => $model->getFullAddress(),
                    'address_id' => $model->address_id
                ];
            }
            return ['result' => true, 'data' => $data];
        }

        return ['result' => true, 'data' => null];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    protected function searchAddressObject($params)
    {
        $query = FiasAddressObject::find()->where(['IN', 'address_level', $this->levels]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'region_code' => $this->region,
        ]);

        $query->andFilterWhere([
            'LIKE',
            'title',
            $this->street
        ]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    protected function searchHouse($params)
    {
        $query = FiasHouse::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->where(['address_id' => $this->address_id]);

        $query->andFilterWhere([
            'LIKE',
            FiasHouse::tableName() . '.number',
            $this->house
        ]);

        return $dataProvider;
    }
}