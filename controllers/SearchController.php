<?php


namespace solbianca\fias\controllers;

use yii\web\Controller;

class SearchController extends Controller
{
    /**
     * @inherit
     *
     * @return array
     */
    public function actions()
    {
        return [
            'autocomplete' => [
                'class' => 'solbianca\fias\actions\AutocompleteAction'
            ],
        ];
    }
}