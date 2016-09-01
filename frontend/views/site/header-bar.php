<?php
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\widgets\Alert;

$masquerading = Yii::$app->session->get('internal_user') && Yii::$app->session->get('current_account') ;

$menuOptions = [
    'brandLabel' => 'EDSR (Front End)',
    'brandUrl'   => Yii::$app->homeUrl,
    'options'    => [
        'class' => 'nav-exertis navbar-fixed-top',
    ]
] ;

$menuItems = [] ;

if (!($isGuest = Yii::$app->user->isGuest)) {
    if (Yii::$app->user->can('buy_for_customer')) {
        $menuItems = [
//            ['label' => 'Shop', 'url' => ['/shop']]
        ];

    } elseif ($masquerading) {
        if(Yii::$app->user->identity->shopEnabled){
            $menuItems = [
                ['label' => 'Shop', 'url' => ['/shop']],
                ['label' => 'Back to Dashboard from ' . Yii::$app->user->displayName,
                    'url' => ['/dashboard'],
                    'linkOptions' => ['data-method' => 'get']
                ]
            ];
        } else {
            $menuItems = [
                ['label' => 'Back to Dashboard from ' . Yii::$app->user->displayName,
                    'url' => ['/dashboard'],
                    'linkOptions' => ['data-method' => 'get']
                ]
            ];
        }
        

    } else {
        if(Yii::$app->user->identity->shopEnabled){
            $menuItems[] = ['label' => 'Shop', 'url' => ['/shop'],
            ];
        }
        
    }

        $menuItems[] = ['label' => 'Stockroom', 'url' => ['/']];
        $menuItems[] = ['label' => 'Order History', 'url' => ['/orders']];
        
        if(Yii::$app->user->identity->role_id = \common\models\EDSRRole::ROLE_MAINUSER){
            $menuItems[] = ['label' => 'Invoices', 'url' => ['javascript:alert("Coming Soon!")']];
        }
        
        $menuItems[] = ['label' => 'News', 'url' => ['/site/news'], 'cssclass' => 'visible-md-inline-block visible-lg-inline-block '];
        $menuItems[] = ['label' => 'Legal', 'url' => ['/site/legal']];
        $menuItems[] = ['label' => 'Help', 'url' => ['/site/help']];
        $menuItems[] = ['label' => 'Account ('.Yii::$app->user->displayName.')', 'url' => ['/user/account']];
}


if (($isGuest = Yii::$app->user->isGuest)) {
//    $menuItems[] = ['label' => 'Signup', 'url' => ['#signup']];
    $menuItems[] = ['label' => 'Help', 'url' => ['/site/help']];
    $menuItems[] = ['label' => 'Login', 'url' => ['#login']];

} elseif  (!$masquerading) {

    $user = \Yii::$app->user->getIdentity();
    if ($user->can('add_customer_user')) {
        $menuItems[] = [
            'label' => '<span class="glyphicon glyphicon-cog" title="Settings"></span>',
            'url'   => ['/settings']
        ];
    }
    
    $menuItems[] = [
        'label' => '<span class="glyphicon glyphicon-log-out" title="Logout"></span>',
        'url' => ['/site/logout'],
        'linkOptions' => ['data-method' => 'post']
    ];
}

if ($isAjax) {
    
    if (isset(Yii::$app->params['maintenanceMode'])) {
        $now = time();
        $start = strtotime(Yii::$app->params['maintenanceMode']['start']);
        $end = strtotime(Yii::$app->params['maintenanceMode']['end']);
        $maintenancemode = ($now > $start) && ($now < $end);
    } else {
        // no param in frontend/config/params.php
        $maintenancemode = false;
    }
    
    echo json_encode(['options' => $menuOptions, 
        'nav' => $menuItems, 
        'loggedIn' => !$isGuest, 
        'useGauthify' => isset(Yii::$app->params['useGauthify']) ? Yii::$app->params['useGauthify'] : false, 
        'passwordreset'=>0,
        'maintenancemode' => $maintenancemode 
            ]
            ) ;

} else {

?>

    <header>
        <div class="wrapper clearfix">
            <h1><a href="http://www.exertis.co.uk">Exertis</a></h1>

            <div class="wrap">
                <?php

                if (!$isAjax) {
                    NavBar::begin(
                        $menuOptions
                    );
                    echo Nav::widget([
                        'options' => ['class' => 'navbar-nav navbar-right'],
                        'items'   => $menuItems,
                    ]);

                    NavBar::end();
                } else {
                    echo json_encode(['options' => $menuOptions, 'nav' => $menuItems]) ;
                }
                ?>

                <div class="container">
                    <?= Breadcrumbs::widget([
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]) ?>
                    <?= Alert::widget() ?>
                </div>
            </div>
        </div>
    </header>

    <header>
        <div class="wrapper clearfix">
            <h1><a href="http://www.exertis.com">Exertis</a></h1>
            <nav>
                <ul id="menu-header" class="menu"><li id="menu-item-295" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-295"><a href="http://www.exertis.com/about-us/">About Us</a>
                        <ul class="sub-menu">
                            <li id="menu-item-296" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-296"><a href="http://www.exertis.com/about-us/about-exertis/">About Exertis</a></li>
                            <li id="menu-item-297" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-297"><a href="http://www.exertis.com/about-us/executive-team/">Executive Team</a></li>
                            <li id="menu-item-298" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-298"><a href="http://www.exertis.com/about-us/dcc-plc/">DCC plc</a></li>
                        </ul>
                    </li>
                    <li id="menu-item-299" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-299"><a href="http://www.exertis.com/companies/">Companies</a>
                        <ul class="sub-menu">
                            <li id="menu-item-300" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-300"><a href="http://www.exertis.com/companies/exertis-supplies/">Exertis Supplies</a></li>
                            <li id="menu-item-301" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-301"><a href="http://www.exertis.com/companies/exertis-banque-mangetique/">Exertis Banque Magnétique</a></li>
                            <li id="menu-item-302" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-302"><a href="http://www.exertis.com/companies/exertis-comtrade/">Exertis Comtrade</a></li>
                            <li id="menu-item-303" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-303"><a href="http://www.exertis.com/companies/exertis-home/">Exertis Home</a></li>
                            <li id="menu-item-304" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-304"><a href="http://www.exertis.com/companies/exertis-go-connect/">Exertis GO Connect</a></li>
                            <li id="menu-item-305" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-305"><a href="http://www.exertis.com/companies/exertis-ireland/">Exertis Ireland</a></li>
                            <li id="menu-item-306" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-306"><a href="http://www.exertis.com/companies/exertis-it/">Exertis IT</a></li>
                            <li id="menu-item-307" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-307"><a href="http://www.exertis.com/companies/exertis-mse/">Exertis MSE</a></li>
                            <li id="menu-item-308" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-308"><a href="http://www.exertis.com/companies/exertis-supply-chain-services/">Exertis Supply Chain Services</a></li>
                            <li id="menu-item-309" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-309"><a href="http://www.exertis.com/companies/exertis-ztorm/">Exertis Ztorm</a></li>
                        </ul>
                    </li>
                    <li id="menu-item-310" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-310"><a href="http://www.exertis.com/expertise/">Expertise</a>
                        <ul class="sub-menu">
                            <li id="menu-item-311" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-311"><a href="http://www.exertis.com/expertise/growing-your-business/">Growing Your Business</a></li>
                            <li id="menu-item-312" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-312"><a href="http://www.exertis.com/expertise/technology-focus/">Technology Focus</a></li>
                            <li id="menu-item-313" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-313"><a href="http://www.exertis.com/expertise/customer-channels/">Customer Channels</a></li>
                            <li id="menu-item-314" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-314"><a href="http://www.exertis.com/expertise/supply-chain-services/">Supply Chain Services</a></li>
                        </ul>
                    </li>
                    <li id="menu-item-315" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-315"><a href="http://www.exertis.com/countries/">Countries</a>
                        <ul class="sub-menu">
                            <li id="menu-item-316" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-316"><a href="http://www.exertis.com/countries/uk/">United Kingdom</a></li>
                            <li id="menu-item-317" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-317"><a href="http://www.exertis.com/countries/france/">France</a></li>
                            <li id="menu-item-318" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-318"><a href="http://www.exertis.com/countries/ireland/">Ireland</a></li>
                            <li id="menu-item-319" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-319"><a href="http://www.exertis.com/countries/netherlands/">Netherlands</a></li>
                            <li id="menu-item-320" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-320"><a href="http://www.exertis.com/countries/sweden/">Sweden</a></li>
                            <li id="menu-item-321" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-321"><a href="http://www.exertis.com/countries/belgium/">Belgium</a></li>
                            <li id="menu-item-322" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-322"><a href="http://www.exertis.com/countries/poland/">Poland</a></li>
                            <li id="menu-item-323" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-323"><a href="http://www.exertis.com/countries/china/">China</a></li>
                            <li id="menu-item-324" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-324"><a href="http://www.exertis.com/countries/usa/">USA</a></li>
                        </ul>
                    </li>
                    <li id="menu-item-325" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-325"><a href="http://www.exertis.com/partners/">Partners</a>
                        <ul class="sub-menu">
                            <li id="menu-item-326" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-326"><a href="http://www.exertis.com/partners/vendors/">Vendors</a></li>
                            <li id="menu-item-327" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-327"><a href="http://www.exertis.com/partners/customers/">Customers</a></li>
                        </ul>
                    </li>
                    <li id="menu-item-328" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-328"><a href="http://www.exertis.com/contact/">Contact</a></li>
                </ul>			</nav>
        </div>
    </header>
    <?php
}
