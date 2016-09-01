<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

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
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => 'EDSR Back End',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            $menuItems = [
                ['label' => 'Home', 'url' => ['/site/index']],
            ];
            if (Yii::$app->user->isGuest) {
                // Guests see this
                $menuItems[] = ['label' => 'Login', 'url' => ['/user/login']];
            } else {

                if (Yii::$app->user->can("admin")) {
                    // Only Admin users see this
                    $menuItems[] = ['label'=>'Admin',
                    				'items'=>[
                                                            ['label' => 'User Roles', 'url' => ['/role/index']],
                                                            ['label' => 'Users', 'url' => ['/gauser']],
                                                         ]
                                    ];
                    $menuItems[] = ['label'=>'Manage Data',
                    				'items'=>[
								            ['label' => 'Digital Products', 'url' => ['/digitalproduct/index']],
								            ['label' => 'Customers', 'url' => ['/customer/index']],
								            ['label' => 'Account-SOP Lookup', 'url' => ['/account-sop-lookup/index']],
								            ['label' => 'Order Details', 'url' => ['/orderdetails/index']],
								            ['label' => 'Product Lookup', 'url' => ['/productcode-lookup/index']],
								            ['label' => 'DigitalProduct-Ztorm Mapping', 'url' => ['/digitalproduct/ztorm-catalogue']],
								            ['label' => 'Order Search', 'url' => ['/orderdetails/search']],
 										]
									];
                    $menuItems[] = ['label'=>'Stock & Stock Rooms',
                    				'items'=>[
											Yii::$app->user->can("admin") ? ['label' => 'Stock Items', 'url' => ['/stockitem/index']] : [],
											['label' => 'Stock Items (Sales View)', 'url' => ['/stockitem/sales-index']],
										    Yii::$app->user->can("admin") ? ['label' => 'Stock Rooms', 'url' => ['/stockroom/index']] : [],
 										]
									];
                    
                    $menuItems[] = ['label'=>'Accounts',
                    				'items'=>[
                                                            ['label' => 'Accounts', 'url' => ['/account/index']],
                                                            ['label' => 'Account Rules', 'url' => ['/account-rule/index']],
                                                            ['label' => 'Account API Credentials', 'url' => ['/api-credentials/index']],
                                                            ['label' => 'API Callback Requests', 'url' => ['/api-callback/index']],
                                                         ]
                                    ];

                    } // can admin

                if (Yii::$app->user->can("monitor_sales")) {
                    $menuItems[] = ['label'=>'Stock & Stock Rooms',
                    				'items'=>[
										['label' => 'Stock Items (Sales View)', 'url' => ['/stockitem/sales-index']],
 										]
									];

                    //$menuItems[] = ['label' => 'Cupboard Items', 'url' => ['/cupboarditem/index']];
                }

                // logged in Users see this

                $menuItems[] = [
                    'label' => 'Logout (' . Yii::$app->user->displayName . ')',
                    'url' => ['/user/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ];
            }
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems,
            ]);
            NavBar::end();
        ?>

        <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
        <p class="pull-left">&copy; Exertis <?= date('Y') ?></p>
        <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
