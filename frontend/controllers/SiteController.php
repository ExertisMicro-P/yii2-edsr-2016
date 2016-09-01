<?php
namespace frontend\controllers;

use common\models\StockActivity;
use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\EDSRRole;
use frontend\models\RegisterForm;
use common\models\gauth\GAUser;
use yii\web\ErrorAction;



/**
 * Site controller
 */
class SiteController extends Controller
{
    public  $layout = '@frontend/views/layouts/mainnw';

    // -----------------------------------------------------------------------
    // For the STOCK CONTROLLER module
    // -----------------------------------------------------------------------
    protected $user ;
    protected $userType ;                 // Set to a for admin, u for user and c for customer
    protected $stockroomId ;              // The current stock room id.

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup', 'about'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['about'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post', 'get'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($event)
    {

        if (!$event instanceof ErrorAction) {
            if (Yii::$app->controller->id <> 'dashboard') {
                if (Yii::$app->session->get('internal_user')) {
                    if (!Yii::$app->session->get('masquerade_user')) {
                        return $this->redirect('dashboard');
                    }
                    $user = GAUser::findById(Yii::$app->session->get('masquerade_user'));
                    Yii::$app->user->setIdentity($user);

                }
            }
        }
        return parent::beforeAction($event);
    }


    public function actionAbout() {

    }

    /**
     * INDEX
     * =====
     * Available to all normal users and to admin users who are masquerading
     * as a normal user
     * 
     * RCH 20160309 This is an known entry point
     * RCH 20160112 - See srflatcontroller/index... that's where login happens now
     * 
     *
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        // RCH 20160309
        // Check for Maintenance Mode
        if (isset(Yii::$app->params['maintenanceMode'])) {
            $now = time();
            $start = strtotime(Yii::$app->params['maintenanceMode']['start']);
            $end = strtotime(Yii::$app->params['maintenanceMode']['end']);
            $maintenancemode = ($now > $start) && ($now < $end);

            \yii::info(__METHOD__.': Checking Maintenance mode - now='.date("Y-m-d H:i:s", $now).' / params='.print_r(Yii::$app->params['maintenanceMode'],true));
        
            if ($maintenancemode) {
                \yii::info(__METHOD__.': Maintenance Mode Active!');
                return $this->render('maintenancemode', ['maintenancemodeparams' => Yii::$app->params['maintenanceMode']]);
            }
        }
        
        if (Yii::$app->user && Yii::$app->user->identity &&
            Yii::$app->user->identity->role_id == EDSRRole::ROLE_INTERNAL) {
            \Yii::info(__METHOD__.': redirecting to '.Yii::$app->params["backEndServer"]);
            return $this->redirect(Yii::$app->params["backEndServer"]) ;
        }

        if (\Yii::$app->user->isGuest) {
            \Yii::info(__METHOD__.': Guest! Showing default HomePage');
            return $this->showDefaultHomePage() ;
        }
        return $this->showLoggedInPage() ;
    }

    /**
     * LOGIN
     * =====
     * If the login succeeds, check that the user is a normal one, redirecting
     * any admin users to the back end to log in there.
     *
     * THIS IS NOT USED - 
     * RCH 20160112 - See srflatcontroller/index... that's where login happens now
     *
     * @return string|\yii\web\Response
     */
    /*
    public function actionLogin()
    {

        if (!\Yii::$app->user->isGuest) {
//            return $this->goHome();
        }

        if (Yii::$app->user->identity->role_id == EDSRRole::ROLE_INTERNAL ||
            Yii::$app->user->identity->role_id == EDSRRole::ROLE_ADMIN) {
            \yii::info(__METHOD__.': redirecting to '.Yii::$app->params["backEndServer"]);
            return $this->redirect(Yii::$app->params["backEndServer"]) ;
        }


        $model = Yii::$app->getModule("user")->model("LoginForm");
        if ($model->load(Yii::$app->request->post()) && $model->login(Yii::$app->getModule("user")->loginDuration)) {
            //echo 'ok';
            \yii::info(__METHOD__.': going back');
            return $this->goBack();
        } else {
            \yii::info(__METHOD__.': rendering login');
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
     * 
     */

//    public function actionEdsr()
//    {
//        $this->layout = 'mainnw';
//
//        if (Yii::$app->request->isPost) {
//            die('got it');
//        }
//
//
//        $isAjax = Yii::$app->request->isAjax;
//
//        return $this->displayHomePage($isAjax);
//
//    }
//
//
//    private function displayHomePage($isAjax) {
//        if (!$isAjax) {
//            $model = Yii::$app->getModule("user")->model("LoginForm");
//
//            $authAPIKey = isset(Yii::$app->params['authify']) ? Yii::$app->params['authify'] : null ;
//
//            $registermodel = new RegisterForm();
//
//
//            if ($model->load(Yii::$app->request->post()) && $model->login(Yii::$app->getModule("user")->loginDuration)) {
//                return $this->goBack(Yii::$app->getModule("user")->loginRedirect);
//            }
//            return $this->render ('edsr', [
//                'model' => $model,
//                'registermodel' => $registermodel,
//                'isAjax'        => $isAjax
//            ]);
//
//
//        }
//
//    }


    /**
     * LOGOUT
     * ======
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * CONTACT
     * =======
     * @return string|\yii\web\Response
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * HELP
     * ====
     * @return string
     */
    public function actionHelp()
    {
        return $this->render('help');
    }


    /**
     * NEWS
     * ====
     * @return string
     */
    public function actionNews()
    {
        return $this->render('news');
    }



    public function actionLegal()
    {
        return $this->render('legal');
    }

    /**
     * SIGNUP
     * ======
     * @return string|\yii\web\Response
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    // RCH 20150126 I don't think this works or is used
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * RESET PASSWORD
     * ==============
     * @param $token
     *
     * @return string|\yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }



    /**
     * SHOW DEFAULT HOMEPAGE
     * =====================
     * This displays the page shown to a guest user
     */
    private function showDefaultHomePage () {

        $model = Yii::$app->getModule("user")->model("LoginForm");

        $registermodel = new RegisterForm();

        if ($model->load(Yii::$app->request->post()) && $model->login(Yii::$app->getModule("user")->loginDuration)) {
            return $this->goBack(Yii::$app->getModule("user")->loginRedirect);

        } else {
            Yii::trace('I\'m in '.__METHOD__.':'.__LINE__);

            // This will show the login popup
            return $this->render('index', [
                'model' => $model,
                'registermodel' => $registermodel,
                'usegauthify' => Yii::$app->params['useGauthify'],
            ]);
        }
    }

    // -----------------------------------------------------------------------
    // For the STOCK CONTROLLER module
    // -----------------------------------------------------------------------

    /**
     * SHOW LOGGED IN PAGE
     * ===================
     */
    private function showLoggedInPage () {
        \yii::info(__METHOD__);

        $this->getUserDetails() ;

        $orders = $this->ordertable() ;

        StockActivity::log('Request for stockroom details', $this->stockroomId) ;

        $bodyContent = $this->renderPartial('/yiicomp/stockroom/ordertable-html', [
            'title'         => 'Orders',
            'dataProvider'  => $orders['orderDetails']['provider'],
            'searchModel'   => $orders['orderDetails']['model'],
            'canBuy'        => $orders['accountStatus'] == \common\models\Customer::STATUS_TRANSACTIONAL
        ]);

        return $this->render('customerHome', [
            'orders'      => $orders,
            'bodyContent' => $bodyContent
        ]);

    }

    /**
     * GET USER DETAILS
     * ================
     */
    protected function getUserDetails () {

        $this->user = \Yii::$app->user->getIdentity() ;
        if ($this->user) {

            if ($this->user->can('add_customer_user') ||
                $this->user->can('customer')) {
                $this->userType = 'u';

                // -----------------------------------------------------------
                // Find the current stock room id, defaulting to the first in
                // the table
                // -----------------------------------------------------------
                $this->stockroomId = intval(Yii::$app->request->post('stockroom', 0)) ;

                if (empty($this->stockroomId)) {
                    if (!Yii::$app->session->has('currentStockRoomId')) {
                        \Yii::$app->user->getIdentity()->getStockroomDetails();
                    };

                    $this->stockroomId = Yii::$app->session->get('currentStockRoomId');
                }

            } elseif ($this->user->can('buy_for_customer')) {
                $this->userType = 'bc';


            } elseif ($this->user->can('admin')) {
                $this->userType = 'a';

            } else {
                $this->user = false ;
            }
        }
        
        //\yii::info(__METHOD__.': user='.print_r($this->user->attributes,true));
        return $this->user ;
    }

    /**
     * ORDER TABLE
     * ===========
     * This actually returns the stock items
     *
     * @return string
     */
    public function ordertable () {

        $sHandler = new StockHandler ($this) ;

        $stockRooms = $this->user->getStockroomDetails();

        $results = $sHandler->getOrdertable($stockRooms['stockrooms'][0]['id']) ;
        $results['accountStatus'] = $stockRooms['accountStatus'] ;
        return $results ;
    }

}
