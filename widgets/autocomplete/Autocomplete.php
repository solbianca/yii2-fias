<?php


    namespace solbianca\fias\widgets\autocomplete;

    use solbianca\fias\models\FiasAddressObject;
    use solbianca\fias\models\FiasHouse;
    use solbianca\fias\models\FiasRegion;
    use yii\base\Widget;

    class Autocomplete extends Widget
    {
        /**
         * URL to provide autocomplete
         *
         * @var string
         */
        private $urlAddressObject = '/fias/search/autocomplete';

        /**
         * URL to provide autocomplete
         *
         * @var string
         */
        private $urlHouse = '/fias/search/autocomplete';

        public $model;
        public $attribute;
        public $additional_column;
        public $additional_house_field;

        /**
         * @inherit
         *
         * @return string
         */
        public function run()
        {
            $address = [];
            //если есть фиасеный адресок, то заполняем инпуты
            if($fiasID = $this->model->getAttribute($this->attribute)){
                /** @var FiasHouse $house */
                /** @var FiasAddressObject $street */
                /** @var FiasAddressObject $city*/
                $house = FiasHouse::find()->where(['house_id' => $fiasID])->one();
                if ($house) {
                    $street = FiasAddressObject::find()->where(['address_id' => $house->address_id, 'fias_address_object.address_level' => [7, 91]])->one();
                } else {
                    $street = FiasAddressObject::find()->where(['address_id' => $fiasID, 'fias_address_object.address_level' => [7, 91]])->one();
                }

                $city = null;
                if ($street) {
                    $city = $street->parent;
                    if ($city && !in_array($city->address_level, [4, 6]) && $city->parent && in_array($city->parent->address_level, [4, 6])) {
                        $city = $city->parent;
                    }
                }

                $address = compact(['house', 'street', 'city']);
            }

            return $this->render('index',
                [
                    'urlAddressObject' => $this->urlAddressObject,
                    'urlHouse' => $this->urlHouse,
                    'regions' => $this->getRegions(),
                    'widget' => $this,
                    'address' => $address,
                ]
            );
        }

        public function getRegions()
        {
            $regions = FiasRegion::find()->all();

            if (empty($regions)) {
                return [];
            }

            $result = [];
            foreach ($regions as $region) {
                $result[strval($region->code)] = $region->title;
            }

            return $result;
        }
    }