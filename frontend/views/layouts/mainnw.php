<?php
use yii\helpers\Html;
use frontend\assets\AppAsset;
//use yii\bootstrap\Alert;
use kartik\widgets\Alert;
use cybercog\yii\googleanalytics\widgets\GATracking;

use common\models\PersistantDataLookup; // For checking for global alert messages to be displayed

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?php
    
        $session = Yii::$app->session;
        $session->open();
        
        if($session->get('ssoLogin') == 'true'){
            
            echo '<style type="text/css">'
                . 'header nav.nav-exertis .container-fluid .navbar-header .navbar-brand a {'
                . 'display: none !important;'
                . '}'
                . '</style>';
            
        }
        
    ?>



    <script>
        <?php $isAjax = true ?>

        var navSettings =
        <?php include dirname(__FILE__) . '/../site/header-bar.php' ; ?>

    </script>
    
    
    <?=GATracking::widget([
        'trackingId' => 'UA-45021227-3',
    ])?>


</head>
<body>
<?php $this->beginBody() ?>



<nav-bar params="route: route"
         class="bootstro" data-bootstro-title="Menu"
         data-bootstro-content="Visit your Stock Room, View record of keys sent out, Read the FAQs"
         data-bootstro-width="500px"
         data-bootstro-placement="bottom"
         data-bootstro-step="1"

         >

</nav-bar>



    <div class="container-fluid">
        <home-page params="route : route"></home-page>

        <?php
        // RCH 20150402
        // Check if there are any service alerts in the database
         $alert = PersistantDataLookup::getServiceAlert();

         if (!empty($alert) && $alert['enabled']) {
             /*
            echo Alert::widget([
                'options' => [
                    'class' => 'service-alert alert-'.$alert['type'],
                ],
                'body' => $alert['message']
            ]);
              *
              */

            echo Alert::widget([
                'type' => 'service-alert alert-'.$alert['type'],
                'title' => 'Service Message',
                'icon' => 'glyphicon glyphicon-ok-sign',
                'body' => $alert['message'],
                'showSeparator' => true,

                ]);

         }
        ?>

        <div data-bind="fadeVisible: route && route().request_ == ''">
            <?= $content ?>
        </div>
    </div>
<?php $this->endBody() ?>


<script data-main="app/startup" src="/edsr/src/bower_modules/knockout/dist/knockout.js"></script>
<script data-main="app/startup" src="/edsr/src/bower_modules/knockout-projections/dist/knockout-projections.js"></script>
<script data-main="app/startup" src="/edsr/src/bower_modules/knockout-postbox/build/knockout-postbox.min.js"></script>




<script src="/edsr/src/app/require.config.js"></script>
<script data-main="app/startup" src="/edsr/src/bower_modules/requirejs/require.js"></script>



<!--Start Cookie Script http://cookie-script.com/ -->
<script type="text/javascript" charset="UTF-8" src="//cookie-script.com/s/9cceb32c179b384685101a54cbd5b1ba.js"></script>
<!--End Cookie Script-->
<script type="text/javascript" charset="UTF-8" src="/js/jquery.expose.js"></script>

<!-- NAGIOS CHECK -->
</body>
</html>
<?php $this->endPage() ?>

