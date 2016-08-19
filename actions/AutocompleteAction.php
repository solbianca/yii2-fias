<?php
namespace solbianca\fias\actions;

use solbianca\fias\searches\SearchAddress;
use Yii;
use yii\helpers\Json;

class AutocompleteAction extends \yii\base\Action
{
    public function run()
    {
        $model = new SearchAddress();
        return Json::encode($model->searchAddress(Yii::$app->request->queryParams));
    }
}