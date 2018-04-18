<?php

    use yii\helpers\Html;

    /**
     * @var $this yii\web\View
     * @var $regions \solbianca\fias\models\FiasRegion[]
     * @var $widget \solbianca\fias\widgets\autocomplete\Autocomplete
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

    $region = '';
    $city = $street = $house = [];
    $house = [];
    if($address){
        if ($address['house']) {
            $house = [['id' => $address['house']->house_id, 'value' => $address['house']->getFullNumber(), 'mark' => 1]];
        }

        if ($address['street']) {
            $street = [['id' => $address['street']->address_id, 'value' => $address['street']->getShortAddress(), 'mark' => 1]];
            if ($address['city']) {
                $city = [['id' => $address['city']->address_id, 'value' => $address['city']->getShortAddress(), 'mark' => 1]];
                $region = $address['city']->region_code;
            }
        }
    }
?>

<div id="form-address-<?= $widget->id ?>">

    <div class="form-group">
        <label for="form-region">Регион</label>
        <?php
            echo \kartik\widgets\Select2::widget([
                'language' => 'ru',
                'name' => 'region',
                'id' => 'region_' . $widget->id,
                'data' => $regions,
                'value' => $region,
                'options' => ['placeholder' => 'Выберите регион...', 'onChange' => "$('#city_" . $widget->id . "_ajaxDropDownWidget .ajaxDropDownSingleRemove').click();",],
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
                'id' => 'city_input' . $widget->id,
                'name' => 'city_' . $widget->id,
                'source' => \yii\helpers\Url::to('/fias/search/autocomplete'),
                'singleMode' => true,
                'keyTrigger' => true,
                'buttonsClass' => 'btn-default',
                'delay' => 2000,
                'minQuery' => 4,
                'getAdditionalPostData' => '[{find:"city", region:$("select[id=region_' . $widget->id . ']").val()}]',
                'local' => $ajaxDropdownLocal,
                'data' => $city,
                'onSelect' => "$('#street_" . $widget->id . "_ajaxDropDownWidget .ajaxDropDownSingleRemove').click();",
                'onRemove' => "$('#street_" . $widget->id . "_ajaxDropDownWidget .ajaxDropDownSingleRemove').click();",
                'inputOptions' => [
                    'autocorrect' => 'off',
                    'autocapitalize' => 'off',
                    'autocomplete' => 'off',
                ],
            ]);
        ?>
    </div>
    <div class="form-group">
        <label for="form-street">Улица</label>
        <?php
            echo \bizley\ajaxdropdown\AjaxDropdown::widget([
                'id' => 'street_input' . $widget->id,
                'name' => 'street_' . $widget->id,
                'source' => \yii\helpers\Url::to('/fias/search/autocomplete'),
                'singleMode' => true,
                'keyTrigger' => true,
                'buttonsClass' => 'btn-default',
                'delay' => 2000,
                'minQuery' => 4,
                'getAdditionalPostData' => '[{find:"street", region:$("select[id=region_' . $widget->id . ']").val(), city_id:$("input[name=city_' . $widget->id . ']").val()}]',
                'local' => $ajaxDropdownLocal,
                'data' => $street,
                'onSelect' => "$('#" . Html::getInputId($widget->model,$widget->attribute) . '_ajaxDropDownWidget' . " .ajaxDropDownSingleRemove').click();",
                'onRemove' => "$('#" . Html::getInputId($widget->model,$widget->attribute) . '_ajaxDropDownWidget' . " .ajaxDropDownSingleRemove').click();",
                'inputOptions' => [
                    'autocorrect' => 'off',
                    'autocapitalize' => 'off',
                    'autocomplete' => 'off',
                ],
            ]);
        ?>
    </div>
    <div class="form-group">
        <label for="form-house">Номер дома</label>
        <?php
            echo \bizley\ajaxdropdown\AjaxDropdown::widget([
                'id' => 'house_input' . $widget->id,
                'model' => $widget->model,
                'attribute' => $widget->attribute,
                'source' => \yii\helpers\Url::to('/fias/search/autocomplete'),
                'singleMode' => true,
                'keyTrigger' => true,
                'buttonsClass' => 'btn-default',
                'delay' => 2000,
                'minQuery' => 1,
                'getAdditionalPostData' => '[{find:"house", region:$("select[id=region_' . $widget->id . ']").val(), city_id:$("input[name=city_' . $widget->id . ']").val(), street_id:$("input[name=street_' . $widget->id . ']").val()}]',
                'local' => $ajaxDropdownLocal,
                'data' => $house,
//                'onSelect' => 'alert("house")',
                'inputOptions' => [
                    'autocorrect' => 'off',
                    'autocapitalize' => 'off',
                    'autocomplete' => 'off',
                ],
            ]);
        ?>
    </div>
    <?php if ($widget->additional_house_field) { ?>
        <div class="form-group">
            <label for="form-house">Номер дома *(при отсутствии в списке)</label>
            <?= Html::input('text', Html::getInputName($widget->model, $widget->additional_house_field), $widget->model->getAttribute($widget->additional_house_field),['class' => 'form-control']) ?>
        </div>
    <?php } ?>
    <?php if ($widget->additional_column) { ?>
        <div class="form-group">
            <label for="form-house">Дополнительно (квартира, помещение)</label>
            <?= Html::input('text', Html::getInputName($widget->model, $widget->additional_column), $widget->model->getAttribute($widget->additional_column),['class' => 'form-control']) ?>
        </div>
    <?php } ?>
</div>