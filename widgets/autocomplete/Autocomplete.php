<?php


namespace solbianca\fias\widgets\autocomplete;

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

    /**
     * @inherit
     *
     * @return string
     */
    public function run()
    {
        return $this->render('index',
            [
                'urlAddressObject' => $this->urlAddressObject,
                'urlHouse' => $this->urlHouse,
                'regions' => $this->getRegions()
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