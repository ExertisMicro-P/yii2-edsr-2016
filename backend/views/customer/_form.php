<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var common\models\Customer $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="customer-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [

'exertis_account_number'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Exertis Account Number...', 'maxlength'=>20]], 

'name'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Name...']], 

'timestamp'=>['type'=> Form::INPUT_WIDGET, 'widgetClass'=>DateControl::classname(),'options'=>['type'=>DateControl::FORMAT_DATETIME]], 

'vat_code'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Vat Code...', 'maxlength'=>20]], 

'fixed_shipping_charge'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Fixed Shipping Charge...', 'maxlength'=>20]], 

'status'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Status...', 'maxlength'=>1]], 

'fixed_shipping_flag'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Fixed Shipping Flag...', 'maxlength'=>1]], 

'payment_terms'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Payment Terms...', 'maxlength'=>1]], 

'invoicing_address_line1'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Invoicing Address Line1...', 'maxlength'=>240]], 

'invoicing_address_line2'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Invoicing Address Line2...', 'maxlength'=>240]], 

'invoicing_address_line3'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Invoicing Address Line3...', 'maxlength'=>240]], 

'invoicing_address_line4'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Invoicing Address Line4...', 'maxlength'=>240]], 

'delivery_address_line1'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Delivery Address Line1...', 'maxlength'=>240]], 

'delivery_address_line2'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Delivery Address Line2...', 'maxlength'=>240]], 

'delivery_address_line3'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Delivery Address Line3...', 'maxlength'=>240]], 

'delivery_address_line4'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Delivery Address Line4...', 'maxlength'=>240]], 

'invoicing_postcode'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Invoicing Postcode...', 'maxlength'=>60]], 

'invoicing_city'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Invoicing City...', 'maxlength'=>60]], 

'delivery_postcode'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Delivery Postcode...', 'maxlength'=>60]], 

'delivery_city'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Delivery City...', 'maxlength'=>60]], 

'invoicing_country_code'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Invoicing Country Code...', 'maxlength'=>10]], 

'delivery_country_code'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Delivery Country Code...', 'maxlength'=>10]], 

'phone_number'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Phone Number...', 'maxlength'=>45]], 

'shipping_method'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Shipping Method...', 'maxlength'=>45]], 

'unknown1'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Unknown1...', 'maxlength'=>45]], 

'unknown2'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Unknown2...', 'maxlength'=>45]], 

    ]


    ]);
    echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end(); ?>

</div>
