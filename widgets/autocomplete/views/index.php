<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use solbianca\fias\widgets\autocomplete\AutocompleteAsset;

AutocompleteAsset::register($this);

/**
 * @var $this yii\web\View
 * @var $urlAddressObject string
 * @var $urlHouse string
 * @var $regions \solbianca\fias\models\FiasRegion[]
 */

$js = <<<EOD

var autocomplete = {

    'config' : {
        'urlAddressObject': '{$urlAddressObject}',
        'urlHouse': '{$urlHouse}',
        'container' : $('#form-address'),
        'streetInput': '#form-street',
        'streetIdInput': '#form-street-id',
        'houseInput': '#form-house',
    },

    'events': function() {
        $(autocomplete.config.streetInput).keyup(function(event) {
            autocomplete.getAddresses(autocomplete.config.urlAddressObject ,autocomplete.getFormData(), 'street');
        });

        $(autocomplete.config.houseInput).keyup(function(event) {
            autocomplete.getAddresses(autocomplete.config.urlHouse, autocomplete.getFormData(), 'house');
        });
    },

    'init' : function(config) {
        if (config && typeof(config) == 'object') {
            $.extend(myFeature.config, config);
        }
        autocomplete.events();
    },

    'getFormData': function() {
        return $(autocomplete.config.container).serializeArray();
    },

    'getAddresses': function(url, formData, type) {
        var request = $.ajax({
            url: url,
            method: "GET",
            data: formData,
            dataType: "json"
        });

        request.done(function( respond ) {
            if (respond.result !== true) {
                return null;
            } else if (respond.data === null) {
                return null;
            }

            if (type === 'street') {
                autocomplete.initStreetAutocomplete(respond.data);
            } else if (type === 'house') {
                autocomplete.initHouseAutocomplete(respond.data);
            }
        });

        request.fail(function( jqXHR, textStatus ) {
            console.log( "Что-то пошло не так." );
        });
    },

    'initStreetAutocomplete': function(data) {
        $(autocomplete.config.streetInput).autocomplete({
            minLength: 3,
            source: data,
            select: function( event, ui ) {
                $(autocomplete.config.streetInput).val( ui.item.value );
                $(autocomplete.config.streetIdInput).val( ui.item.address_id );         
                return false;
            }
        });
    },

    'initHouseAutocomplete': function(data) {
        $(autocomplete.config.houseInput).autocomplete({
            minLength: 0,
            source: data
        });
    }
};
autocomplete.init();
EOD;
$this->registerJs($js);
?>

<?php $form = ActiveForm::begin([
    'id' => 'form-address',
]) ?>

    <div class="form-group">
        <label for="form-region">Регион</label>
        <?= Html::dropDownList('region', 77, $regions, ['class' => 'form-control', 'if' => 'form-region']) ?>
    </div>
    <div class="form-group">
        <label for="form-city">Город</label>
        <?= Html::textInput('city', null, ['class' => 'form-control address-form', 'id' => 'form-city']) ?>
        <?= Html::hiddenInput('address_id', null, ['id' => 'form-city-id']) ?>
    </div>
    <div class="form-group">
        <label for="form-street">Улица</label>
        <?= Html::textInput('street', null, ['class' => 'form-control address-form', 'id' => 'form-street']) ?>
        <?= Html::hiddenInput('address_id', null, ['id' => 'form-street-id']) ?>
    </div>
    <div class="form-group">
        <label for="form-house">Номер дома</label>
        <?= Html::textInput('house', null, ['class' => 'form-control address-form', 'id' => 'form-house']) ?>
    </div>

<?php ActiveForm::end() ?>