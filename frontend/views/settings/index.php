<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use kartik\grid\GridView;


/* @var $this yii\web\View */
$this->title                   = 'Settings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about container">
    
    <?php
        //$role = common\models\EDSRRole::find()->where(['id'=>Yii::$app->user->identity->role_id])->one()->name;
        
        if(Yii::$app->user->can('add_customer_user')){
    ?>
            <h1><?= Html::encode($this->title) ?> for account <?= $account->customer_exertis_account_number ?></h1>
        <h2><?= $account->customer->name ?></h2>

        
    <div class="row helpsection col-md-6">
        <h1>Users</h1>
        
        <h3>Your account currently has <?=$account->getUserCount()?> <?php echo($account->getUserCount() > 1)? 'users' : 'user' ; ?>.</h3>
        
        <p>
            The main user of this account is <b><?=$mainUser->email?></b>.
            <br> 
            All key notification e-mails sent to the main user.
        </p>
        
        <?php
        
            echo GridView::widget([
            'pjax'              => true,
            'pjaxSettings'      => [
                'replace' => false
            ],
            'dataProvider'      => $userActivity,
            'options'           => ['id' => 'orderlist', 'class' => 'grid-view', 'style'=>'color:#000 !important'],

            'toolbar'           => [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-user" style="color:#fff !important"></i> Manage users', '/useradmin', ['title'=>'Manage users', 'class'=>'btn btn-success']),
                ],
            ],

            'toggleDataOptions' => [
                'all' => [
                    'icon'  => '',
                    'label' => ''
                ],
            ],
//        'export'    => false,             // Supposed to hide the export button, but causes javascript error


            'pager'             => [
            ],
            'columns'           => [
                'table_name',
                'message',
                'timestamp',
                'username',
//                [
//                    'class'            => 'kartik\grid\CheckboxColumn',
//                    'rowSelectedClass' => $selectedStockClass,
//                ]
            ],
            'responsive'        => true,
            'hover'             => true,
            'condensed'         => true,
            'floatHeader'       => true,
            'floatHeaderOptions' => ['scrollingTop'           => 0,
                                     'useAbsolutePositioning' => true,
                                     'floatTableClass'        => 'kv-table-float slevel-float hidden-xs hidden-sm',
//                                        'autoReflow' => true          // Doesn't help scrolling problem

            ],


            'panel'             => [
                'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i>&nbsp;Users Recent Activity</h3>',
                'type'    => 'info',
//            'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['create'], ['class' => 'btn btn-success']),
//            'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['index'], ['class' => 'btn btn-info']),
//            'showFooter'=>false
            ],
        ]);
        
        ?>
        
    </div>
   
    <?php
        }
    ?>
    
    
    <div class="row helpsection <?php echo(Yii::$app->user->can('add_customer_user'))? 'col-md-6 pull-right' : 'col-md-12' ?>">
        <h1>Options</h1>

        <?php
        $form = ActiveForm::begin([
            'id'      => 'settings-form',
            'options' => [
                'enctype' => 'multipart/form-data'
            ],
        ])
        ?>

        <div class="row">
            <div class="col-xs-12 col-sm-12 settingshelp  label-danger">
                <?=
                $form->field($account, 'include_key_in_email')->checkbox(array(
                        'label'        => '',
                        'labelOptions' => array('style' => 'padding:5px;')
                    )
                )->label('Include Keys in Email');
                ?>          
                <blockquote>Send me an Email containing the licence keys after purchase.<br>Please note: This is not our recommendation and Exertis cannot be held
                    responsible for any loses incurred through emailing keys.</blockquote>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12 settingshelp label-default">
                <?=
                $form->field($account, 'use_retail_view')->checkbox(array(
                        'label'        => '',
                        'labelOptions' => array('style' => 'padding:5px;')
                    )
                )->label('Use Retail View');
                ?>
           
                <blockquote>Intended for Retail POS use. Hides pricing and shows large boxshots in Shop. Also streamlines Checkout and Print Process</blockquote>
            </div>
        </div>




        <hr/>

        <div class="row">
            <div class="col-xs-12 text-center settings-logo">
                <h4>Your Logo</h4>
            </div>
            <?php
            $template        = <<< _EOF
<div class="col-xs-6 col-sm-6">
            {preview}
</div>

<div class="col-xs-12 col-sm-6">
    <div class="input-group-btn">
        {remove} {cancel} {upload} {action}
    </div>
</div>

_EOF;
            $previewTemplate = <<< _EOF
<div class="file-preview {class} col-md-12">
    <div class="{dropClass}">
        <div class="file-preview-thumbnails">
        </div>
        <div class="clearfix"></div>
        <div class="file-preview-status text-center text-success"></div>
        <div class="kv-fileinput-error"></div>
    </div>
</div>
_EOF;


            // Display an initial preview of files with caption
            // (useful in UPDATE scenarios). Set overwrite `initialPreview`
            // to `false` to append uploaded images to the initial preview.
            echo FileInput::widget([
                'model'         => $account,
                'attribute'     => 'logo',
                'name'          => 'attachment_49[]',
                'options'       => [
                    'multiple' => false,
                    'accept' => 'image/*',
                ],
                'pluginOptions' => [
                    'initialPreview'   => [
                        Html::img("https://res.cloudinary.com/exertis-uk/image/upload/edsr/account_logos/{$account->logo}",
                            ['class' => 'file-preview-image',
                             'alt'   => 'Your account logo',
                             'title' => 'Your account logo']),
                    ],
                    'initialCaption'   => "Your account logo",

                    'showCaption'      => false,
                    'overwriteInitial' => true,
                    'showRemove'       => false,
                    'browseClass'      => 'btn btn-default',
                    'browseIcon'       => '<i class="glyphicon glyphicon-camera"></i> ',
                    'browseLabel'      => 'Select Logo',

                    'showUpload'       => false,

                    'layoutTemplates'  => [
                        'main1'   => $template,
                        'preview' => $previewTemplate
                    ]
                ]]);


            ?>
        </div>
    </div>


    <div class="form-actions">
        <div class="row text-center">
            <div class="col-md-12 <?php echo(Yii::$app->user->can('add_customer_user'))? 'col-md-offset-3' : '' ?>">

                <?= Html::submitButton('<i class="fa fa-save"></i> Save Changes',
                    ['class' => 'btn btn-primary']
                ) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

    <?php if ($success = Yii::$app->session->getFlash('success')) { ?>
        <br/>
        <div class="row">
            <div class="alert alert-info" role="info">
                <?= $success ?>
            </div>
        </div>
    <?php } ?>

    <?php if ($error = Yii::$app->session->getFlash('error')) { ?>
        <br/>
        <div class="row">
            <div class="alert alert-danger" role="alert">
                <?= $error ?>
            </div>
        </div>
    <?php } ?>

</div>


</div>
