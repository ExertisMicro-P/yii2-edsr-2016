<?php
use yii\helpers\Html;
use kartik\widgets\Alert;

$account = \common\models\Account::findOne(['id' => Yii::$app->user->identity->account_id]);
$logoName = $account->customer_exertis_account_number . '-logo.jpg';
$creditBalance = $credit['balance'];
?>

<div class="row srname bootstro"
     data-bootstro-title="Stock Room Info Bar"
     data-bootstro-content="Click the Stock Room name to rename it. Click the logo to upload your own Logo (coming soon). Click green button to deliver License Keys you have selected."
     data-bootstro-width="400px"
     data-bootstro-placement="bottom"
     data-bootstro-step="2"
    >
    <span class=" hidden-xs">
    <div class="col-xs-12 col-sm-6 col-md-4 hidden-sm">
        <span class="" <?php echo($_SERVER['HTTP_REFERER'] == 'http://'.$_SERVER['HTTP_HOST'].'/' || $_SERVER['HTTP_REFERER'] == 'https://'.$_SERVER['HTTP_HOST'].'/')? 'data-bind="clickToEdit: currentStockroomName"' : 'data-bind="html: currentStockroomName"' ; ?>></span>
        <!--
                <a href="#basket" id="bcount" data-bind="click: toggleBasket, attr: {title: selectedCount() > 0 ? 'Click to view your basket' : 'Your basket is currently empty'}">
                    <span class="icon" data-bind="css: {loaded: selectedCount}"></span>
                    <span data-bind="text: selectedCount, visible: selectedCount" class="badge"></span>
                </a>
        -->
    </div>
    <div class="col-xs-12 col-sm-6 col-md-3 hidden-sm">
        <?php if(!empty($account->logo) || $account->logo === $logoName){ ?>
            <img data-bind="attr: {src: currentStockroomAccountLogo}" class="stockroom_account_logo">
        <?php } else { 
            echo Html::a('Upload logo', ['/settings'], ['class'=>'btn btn-info']);
        }
        ?>
    </div>

    <div class="col-xs-12 col-sm-6 col-md-4">
        <?php
            echo Html::button('Items Picked for Delivery
                <span data-bind="text: selectedCount" class="badge"></span>
                <span class="glyphicon glyphicon-chevron-right"></span>',
                ['id'        => 'bcount',
                 'class'     => 'btn btn-success hidden',
                 'data-bind' => "click: toggleBasket,
                                css: {'hidden' : document.location.pathname !== '/' && document.location.pathname.indexOf('/srflat') < 0},
                                attr: {
                                    disabled: !selectedCount(),
                                    title: selectedCount() > 0 ? 'Click to Prepare Delivery' : 'Tick items to pick them for delivery'
                                    }",
                ]);

        ?>
    </div>
    </span>


    <div class="col-xs-12 col-sm-6 col-md-1 blink">
        <stock-basket></stock-basket>
    </div>

    <?php
    //echo Html::button('<span class="icon" data-bind="css: {loaded: selectedCount}"></span>
    echo Html::button('
                <span data-bind="text: selectedCount" class="badge"></span>
                <span class="glyphicon glyphicon-briefcase"></span>',
        ['id'        => 'bcount-xs',
         'class'     => 'btn btn-success visible-xs-inline-block',
         'data-bind' => "click: toggleBasket, attr: {title: selectedCount() > 0 ? 'Click to Prepare Delivery' : 'Tick items to pick them for delivery'}",
        ]);
    ?>

</div>

    
    <div class="col-md-12">
        <?php   

            if($creditBalance < 50 && $creditBalance > 0) {
            
                echo Alert::widget([
                    'type' => Alert::TYPE_WARNING,
                    'title' => 'Your balance is low.',
                    'icon' => 'glyphicon glyphicon-circle-arrow-up',
                    'body' => 'Please call Exertis Accounts on 01282 776776 to Top Up.',
                    'showSeparator' => true,
                    'delay' => false
                ]);

            }
            elseif($creditBalance <= 0) {

                echo Alert::widget([
                    'type' => Alert::TYPE_DANGER,
                    'title' => 'You have no credit balance.',
                    'icon' => 'glyphicon glyphicon-remove-sign',
                    'body' => 'You must top up to buy. Please call Exertis Accounts on 01282 776776 to Top Up.',
                    'showSeparator' => true,
                    'delay' => false
                ]);
                
            }
            
        ?>
    </div>

    <div class="col-md-6 col-md-offset-3">

        <?php
            echo Alert::widget([
                'type' => Alert::TYPE_DANGER,
                'icon' => 'glyphicon glyphicon-remove-sign',
                'body' => 'You can only have "+keyLimit+" products in your basket.',
                'options' => ['class' => 'errorMsg'],
                'showSeparator' => false,
                'delay' => 2000
            ]);
            
        ?>
        
    </div>
