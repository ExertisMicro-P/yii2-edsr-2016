<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use kartik\widgets\Typeahead;
use kartik\widgets\Alert;
use yii\helpers\Url;


\backend\assets\CheckAccountAsset::register($this);

/**
 * @var yii\web\View $this
 * @var amnah\yii2\user\models\Role $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="step1-form">


    <?php
        $form = ActiveForm::begin(['type' => ActiveForm::TYPE_HORIZONTAL]);

        //Hidden field used for set sceanrio if user account found in ORACLE
        echo $form->field($model, 'accountFound')->hiddenInput(['id'=>'accFound'])->label('');

// usage with ActiveForm and model
        echo $form->field($model, 'exertis_account_number')->widget(Typeahead::classname(), [
            'options' => ['placeholder' => 'Filter as you type ...'],
            'pluginOptions' => ['minLength'=>3],
            'dataset' => [
                [
                //    'local' => $account_typeahead_data,
                //    'limit' => 10


                    'display'=>'value',
                    //'prefetch' => $baseUrl . '/samples/countries.json',
                    'remote' => [
                        'url'=>Url::to(['sales-rep-account-email-form/accounts-to-set-up-list']) . '?q=%QUERY',
                        'wildcard' => '%QUERY'
                        ],
                    'limit' => 10
               ]
            ]
        ]);

        echo $form->field($model, 'emailaddress')->
                input('email', ['placeholder' => 'Enter a valid email...'])->
                hint('Email validation input.');

        echo $form->field($model, 'emailaddress_repeat')->
                input('email', ['placeholder' => 'Enter same email again...'])->
                hint('The email address MUST be correct.');

        echo Html::label('Add EDSR shop?', 'edsr', ['class' => 'control-label col-md-2']) ;
        echo Html::checkbox('edsr', false,['class' => 'btn', 'id' => 'edsr']);
        echo '<p>Use this to create a shop without needing an order beforehand.</p>';

        echo $form->field($model, 'xbox')->checkbox()->label(null)->hint('Use this to add xbox rule to this account.');
        
        echo $form->field($model, 'edi_rep')->
        input('edi_rep', ['placeholder' => 'Name of EDI Rep that should be setup in Oracle'])->
        hint('Lisa Bailey will be emailled to check Oracle account is "EDI Ready".');

        echo '<p>&nbsp;</p>';

        echo Html::label('', null, ['class' => 'control-label col-md-2']) ;

        echo Html::submitButton('Save', ['class' => 'btn btn-success']);



        ActiveForm::end();

    ?>

    <div class="col-md-10 pull-right rep_ids">
        <p style="text-align:center">EDI Rep IDs</p>
    </div>



</div>
