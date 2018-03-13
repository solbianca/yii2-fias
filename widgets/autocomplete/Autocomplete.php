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
            if($house_id = $this->model->getAttribute($this->attribute)){
                /** @var FiasHouse $house */
                /** @var FiasAddressObject $street */
                /** @var FiasAddressObject $city*/
                $house = FiasHouse::find()->where(['house_id' => $house_id])->one();
                $street = FiasAddressObject::find()->where(['fias_address_object.address_level' => [7, 91], 'address_id' => $house->address_id])->one();
                $city = $street->parent;
                if ($city && $city->address_level != 4 && $city->parent && $city->parent->address_level == 4) {
                    $city = $city->parent;
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