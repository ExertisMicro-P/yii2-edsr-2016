<?php
use kartik\file\FileInput;
use common\models\ProductLeafletInfo;
use yii\helpers\Html;

use backend\assets\LeafletUploaderAsset;

LeafletUploaderAsset::register($this);

////$this->registerJsFile('/js/jcrop-master/js/jquery.jcrop.min.js', ['depends' => 'yii\web\YiiAsset']);
////$this->registerCssFile('/js/jcrop-master/css/jquery.Jcrop.min.css') ;
?>


<div class="row" id="leaflet-upload">
    <div class="col-xs-12 text-center">
        <h4>Printable Leaflet</h4>
    <!--</div>-->


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
<div class="file-preview {class}">
    <div class="{dropClass}">
        <div class="file-preview-thumbnails">
        </div>
        <div class="clearfix"></div>
        <div class="file-preview-status text-center text-success"></div>
        <div class="kv-fileinput-error"></div>
    </div>
</div>
_EOF;

    // ---------------------------------------------------------------------------
    // Load a reference to the leaflet instance. If the record doesn't exist, we
    // create one now by calling getLeafletImageTab, and then have to explicitly
    // load it in order to make it accessible below
    // ---------------------------------------------------------------------------
    $leaflet = $model->productLeafletInfo;
    
    //die(print_r($leaflet->attributes,true));
    
    if (!$leaflet) {
        $leafletTag = $model->getLeafletImageTag();
        $leaflet    = $model->getProductLeafletInfo()->one(); /// ? $model->productLeafletInfo : new ProductLeafletInfo () ;
    };

    // Display an initial preview of files with caption
    // (useful in UPDATE scenarios). Set overwrite `initialPreview`
    // to `false` to append uploaded images to the initial preview.
    echo FileInput::widget([
        'model'         => $leaflet,
        'attribute'     => 'image',
        'name'          => 'leaflet',
        'id'            => 'leaflet-upload',
        'options'       => [
            'multiple' => false
        ],
        'pluginOptions' => [
            'uploadUrl'                => '/digitalproduct/leaflet?id=' . $model->id,
            'initialPreview'           => [
                Html::img($leaflet->getLeafletWebImageFilename(),
//                Html::img($leaflet->image_data,
                    ['class' => 'file-preview-image',
                     'alt'   => 'Leaflet Image',
                     'title' => 'Leaflet Image']),
            ],
            'initialCaption'           => "Leaflet Image",

            'showCaption'              => false,
            'overwriteInitial'         => true,
            'showRemove'               => false,
            'showDelete'               => true,
            'browseClass'              => 'btn btn-default',
            'browseIcon'               => '<i class="glyphicon glyphicon-camera"></i> ',
            'browseLabel'              => 'Select Leaflet Image',

            'showUpload'               => false,
            'initialPreviewShowDelete' => [false],

            'layoutTemplates'          => [
                'main1'   => $template,
                'preview' => $previewTemplate
            ],
            'allowedFileTypes'         => ['image'],
            'allowedFileExtensions'         => ['jpg', 'png'],
        ],

        'pluginEvents'  => [
            'fileloaded'  => "
                        function () {
                            var select = $(this) ;
                            
                            select.parent().fadeOut() ;
                            $('.kv-file-remove', '#leaflet-upload')
                                .unbind('click.rem')
                                .on('click.rem' , function () {
                                select.parent().fadeIn() ;
                            })
                        }
            ",
            'fileuploaded'  => "
                function () {
                    $('.kv-file-remove', '#leaflet-upload').fadeOut() ;
                    showEditButton();
                 }
             ",

            'filecleared' => "
                function () {
                    console.log('clear')
                    console.log(this);

                    $('#leaflet-upload').find('.btn-file').fadeIn() ;
                }
            ",
            'filedeleted' => "
                function () {
                    $('#leaflet-upload').find('.btn-file').fadeIn() ;
                }
            "
        ]

    ]);


    ?>
</div>
    
    <button id='edit-coords' class='btn btn-primary'>Edit Key Position</button>

<script type="text/html" id="ll-template">

    <div id="leafletContainer">
        <div id="lec">
            <button class="btn btn-xs" id="ll-close" style="float:right;background-color:red">
                <i style="font-size:140%" class="glyphicon glyphicon-ban-circle"></i>
            </button>
            <h5 style="margin: 0">Drag your cursor to mark the area the key or logo is to appear in<h5>

            <div class="infoBlock">
                <div class="iname">
                    <button class="btn btn-info" id="pos-name">Name <i class="glyphicon glyphicon-file"/></button>
                </div>

                <div class="infoData">
                    <span> x: <span id="ln-xc" class="dimens">?</span> w: <span id="ln-w" class="dimens"></span></span><br />
                    <span> y: <span id="ln-yc" class="dimens">?</span> h: <span id="ln-h" class="dimens"></span></span>
                </div>
            </div>

            <div class="infoBlock">
                <div class="iname">
                    <button class="btn btn-info" id="pos-logo">Logo <i class="glyphicon glyphicon-fire"/></button>
                </div>

                <div class="infoData">
                    <span> x: <span id="ll-xc" class="dimens">?</span> w: <span id="ll-w" class="dimens"></span></span><br />
                    <span> y: <span id="ll-yc" class="dimens">?</span> h: <span id="ll-h" class="dimens"></span></span>
                </div>
            </div>

            <div class="infoBlock">
                <div class="iname">
                    <button class="btn btn-info" id="pos-key">Key <i class="glyphicon glyphicon-barcode"/></button>
                </div>

                <div class="infoData">
                    <span> x: <span id="lk-xc" class="dimens">?</span> w: <span id="lk-w" class="dimens"></span></span><br />
                    <span> y: <span id="lk-yc" class="dimens">?</span> h: <span id="lk-h" class="dimens"></span></span>
                </div>
            </div>

            <p id="ll-main">When happy, click <button id="ll-save" style="color:black">Save</button> to save the settings</p>
            <span id='ll-saving'></span>
        </div>

        <div id="leafletFull">
            <img />
            <div id="dummyKey"></div>
        </div>
    </div>
</script>

<?php
// Copy some PHP variables so they are accessible to JS
    
    $s = '$(document).ready(function(){';
    $initTop = $model->productLeafletInfo->key_ycoord ? $model->productLeafletInfo->key_ycoord :  "$('#leafletFull').height()/2 - 12";
    $initLeft = $model->productLeafletInfo->key_xcoord ? $model->productLeafletInfo->key_xcoord :  "$('#leafletFull').width()/2 - 135";
    $key_xcoord = intval($model->productLeafletInfo->key_xcoord);
    $key_ycoord = intval($model->productLeafletInfo->key_ycoord);
    $key_box_width = intval($model->productLeafletInfo->key_box_width);
    $key_box_height = intval($model->productLeafletInfo->key_box_height);
    $logo_xcoord = intval($model->productLeafletInfo->logo_xcoord);
    $logo_ycoord = intval($model->productLeafletInfo->logo_ycoord);
    $logo_box_width = intval($model->productLeafletInfo->logo_box_width);
    $logo_box_height = intval($model->productLeafletInfo->logo_box_height);
    $name_xcoord = intval($model->productLeafletInfo->name_xcoord);
    $name_ycoord = intval($model->productLeafletInfo->name_ycoord);
    $name_box_width = intval($model->productLeafletInfo->name_box_width);
    $name_box_height = intval($model->productLeafletInfo->name_box_height);
    
    //$s .=  'productLeafletInfo = '.json_encode($model->productLeafletInfo->attributes) .';';
    $s .=  'initTop  = '. $initTop .';';
    $s .=  'initLeft = '. $initLeft .';' ;

    $s .= 'model_id = '. $model->id . ';';
    
    $s .=  'key_xcoord = '. $key_xcoord .';' ;
    $s .=  'key_ycoord = '. $key_ycoord .';' ;
    $s .=  'key_box_width = '. $key_box_width .';' ;
    $s .=  'key_box_height = '. $key_box_height .';' ;
    $s .=  'logo_xcoord = '. $logo_xcoord .';' ;
    $s .=  'logo_ycoord = '. $logo_ycoord .';' ;
    $s .=  'logo_box_width = '. $logo_box_width .';' ;
    $s .=  'logo_box_height = '. $logo_box_height .';' ;
    $s .=  'name_xcoord = '. $name_xcoord .';' ;
    $s .=  'name_ycoord = '. $name_ycoord .';' ;
    $s .=  'name_box_width = '. $name_box_width .';' ;
    $s .=  'name_box_height = '. $name_box_height .';' ;
    $s .=  '});'; // end of doc ready

    
    
    $this->registerJs($s, \yii\web\View::POS_END, 'leaflet-vars');
?>

<?php

//ob_start() ;
//include_once (__DIR__ . '/leafleteditor.js') ;
?>








<?php
//$script = ob_get_contents() ;
//ob_end_clean() ;

//$this->registerJs($script) ;
