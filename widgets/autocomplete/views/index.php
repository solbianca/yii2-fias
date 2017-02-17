<?php

use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $regions \solbianca\fias\models\FiasRegion[]
 */

    $ajaxDropdownLocal = [
        'allRecords'        => 'Все записи',
        'error'             => 'Ошибка',
        'minimumCharacters' => 'Необходимо заполнить минимум {NUM} символов',
        'next'              => 'след.',
        'noRecords'         => 'Записей не найдено',
        'previous'          => 'пред.',
        'recordsContaining' => 'Records containing',
    ];
?>

<div id="form-address-<?= $widget->id ?>">

    <div class="form-group">
        <label for="form-region">Регион</label>
        <?php
            echo \kartik\widgets\Select2::widget([
                'language' => 'ru',
                'name' => 'region',
                'id' => 'region',
                'data' => $regions,
                'options' => ['placeholder' => 'Выберите регион...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])
        ?>
    </div>
    <div class="form-group">
        <label for="form-city">Город</label>
        <?php
            echo \bizley\ajaxdropdown\AjaxDropdown::widget([
                'id' => 'city_input',
                'name' => 'city',
                'source' => \yii\helpers\Url::to('/fias/search/autocomplete'),
                'singleMode' => true,
                'keyTrigger' => true,
                'buttonsClass' => 'btn-default',
                'delay' => 2000,
                'minQuery' => 4,
                'getAdditionalPostData' => '[{find:"city", region:$("select[name=region]").val()}]',
                'local' => $ajaxDropdownLocal
            ]);
        ?>
    </div>
    <div class="form-group">
        <label for="form-street">Улица</label>
        <?php
            echo \bizley\ajaxdropdown\AjaxDropdown::widget([
                'id' => 'street_input',
                'name' => 'street',
                'source' => \yii\helpers\Url::to('/fias/search/autocomplete'),
                'singleMode' => true,
                'keyTrigger' => true,
                'buttonsClass' => 'btn-default',
                'delay' => 2000,
                'minQuery' => 4,
                'getAdditionalPostData' => '[{find:"street", region:$("select[name=region]").val(), city_id:$("input[name=city]").val()}]',
                'local' => $ajaxDropdownLocal
            ]);
        ?>
    </div>
    <div class="form-group">
        <label for="form-house">Номер дома</label>
        <?php
            echo \bizley\ajaxdropdown\AjaxDropdown::widget([
                'id' => 'house_input',
                'name' => 'house',
                'source' => \yii\helpers\Url::to('/fias/search/autocomplete'),
                'singleMode' => true,
                'keyTrigger' => true,
                'buttonsClass' => 'btn-default',
                'delay' => 2000,
                'minQuery' => 1,
                'getAdditionalPostData' => '[{find:"house", region:$("select[name=region]").val(), city_id:$("input[name=city]").val(), street_id:$("input[name=street]").val()}]',
                'local' => $ajaxDropdownLocal
            ]);
        ?>
    </div>
</div>