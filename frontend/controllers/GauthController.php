<?php
namespace frontend\controllers;

use Yii;
use yii\base\Exception;
use yii\web\Controller;
use common\models\gauth\GAUser;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use common\models\EDSRRole;
use common\models\StockItem;
use console\components\OrderFeedFile\StockItemEmailer;

use yii\bootstrap\ActiveForm;
use exertis\savewithaudittrail\models\Audittrail;

use common\components\EDSRGAuthify;

class GauthController extends \amnah\yii2\user\controllers\DefaultController {

    public  $layout = '@frontend/views/layouts/mainnw';

    private $gaDetails ;

    const MAX_LIFE_FOR_SETPASSWORD = 600 ;          // 10 minute life for refreshing the QR Code etc

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'confirm', 'resend', 'validate-login', 'check', 'reload-ga'],
                        'allow'   => true,
                        'roles'   => ['?', '@'],
                    ],
                    [
                        'actions' => ['account', 'profile', 'resend-change', 'cancel', 'logout'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                    [
                        'actions' => ['login', 'register', 'forgot', 'reset', 'set-password'],
                        'allow'   => true,
                        'roles'   => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }


    /**
     * CHECK
     * =====
     * Called with a username or email address and checks if the value matches
     * that of an existing user, returning OKif so
     *
     * @return string
     */
    public function actionCheck () {
        $name = Yii::$app->request->get('user') ;

        $user = Yii::$app->getModule("user")->model("User")->findByEmail($name) ;

        return $user ? 'ok' : 'none' ;
    }

    /**
     * @return string
     */
    public function actionLogin() {
        return Yii::$app->getResponse()->redirect('/', 200);
    }

    /**
     * VALIDATE LOGIN
     * ==============
     * This expects to receive the user's email address and password. These are
     * checked and a success or failure return given.
     *
     * The user is not logged in at this point, as they still need to complete the
     * second part of the two factor login
     */
    public function actionValidateLogin()
    {

        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $result = ['result' => 'failed'];
        $ok     = false;

        $model = Yii::$app->getModule("user")->model("LoginForm");
        //$model = new \amnah\yii2\user\models\forms\LoginForm();

        // -------------------------------------------------------------------
        // RCH 20150126
        // Normally we're authenticating a login, but the Login is dual action and also
        // support forgotton password. Check the 'rsf' params to see if the user clicked
        // [Forgotton Password]
        // -------------------------------------------------------------------
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->post('rsf')) {
            // Yes, we're handling a forgotton password. The user has entered their email
            // and we'll send them a reset link by email

            /**
             *  @var \amnah\yii2\user\models\forms\ForgotForm $forgotpw
             */
            $forgotpw = Yii::$app->getModule("user")->model("ForgotForm");

            //$forgotpw = new \amnah\yii2\user\models\forms\ForgotForm();
            $forgotpw->email = $model->username;

            // Attempt to send the forgotten password email to the given email address
            if ($forgotpw->sendForgotEmail()) {
                $result   = ['result' => 'ok'];
                //$ok       = true;
            } else {
                $result   = ['result' => 'error'];
                //$ok       = false;
            }
            return json_encode($result) ;
        }


        // If we get this far, then it's a normal login request (not a reset password)

        if ($model->load(Yii::$app->request->post()) &&
            ($userId = $model->checkLoginDetails(Yii::$app->getModule("user")->loginDuration))
        ) {

            if ($this->validateAuthentication($userId)) {
                if ($model->load(Yii::$app->request->post()) &&
                    $model->login(Yii::$app->getModule("user")->loginDuration)
                ) {
                    if ((Yii::$app->user->identity->role_id == EDSRRole::ROLE_INTERNAL ||
                        Yii::$app->user->identity->role_id == EDSRRole::ROLE_ADMIN) &&
                        !$this->internalUserAccessAllowed()) {

                        Yii::$app->user->logout();

                        $username = Yii::$app->user->displayName;
                        $result   = ['result' => 'internal', 'name' => $username, 'dest' => Yii::$app->params["backEndServer"]];
                        $ok       = true;

                    } else {
                        \session_regenerate_id();
                        $username = Yii::$app->user->displayName;
                        $result   = ['result' => 'ok', 'name' => $username];
                        $ok       = true;
                        
                        //Cleaning up the session delivering table
                        \common\models\SessionDelivering::deleteAll(['created_by' => Yii::$app->user->identity->id]);
                    }
                }
            }
        }

        if (!$ok) {
            $msg = 'Failed login attempt for ' . $model->username . ' from IP ' . Yii::$app->getRequest()->getUserIP();;

            $auditentry = new Audittrail();
            $auditentry->log($msg, 'user', 999999);
        }

//        \Yii::$app->response->format = 'json';
        return json_encode($result) ;
    }

    /**
     * INTERNAL USER ACCESS ALLOWED
     * ============================
     * Validates the current (internal) user via LDAP to see if they are a sales
     * rep and allowed to use the site on behalf of their customers
     *
     * @return bool
     */
    private function internalUserAccessAllowed () {
        if (!Yii::$app->user->can('buy_for_customer')) {
            return false;
        }
        Yii::$app->session->set('internal_user', Yii::$app->user->id) ;
        Yii::$app->session->set('current_account', null) ;
        return true ;
    }
    /**
     * VALIDATE AUTHENTICATION
     * =======================
     * This is responsible for handling the authentication check with GAuthift
     *
     * @return string
     */
    protected function validateAuthentication($uniqueId) {
        if (!Yii::$app->params['useGauthify']) {
            return TRUE; // not using Gauthify, so pretend all is okay
        }

        $gauth = Yii::$app->request->post('Guath') ;
        if (!$gauth || !is_array($gauth) || !array_key_exists('code', $gauth)) {
            return 'failed' ;
        }

        $code = $gauth['code'] ;

        // -------------------------------------------------------------------
        // gauthify throws a number of different exceptions, so check for each
        // -------------------------------------------------------------------
        $gauthify = $this->getGauth() ;
        try {
            return $gauthify->check_auth($uniqueId, $code);

        } catch (\NotFoundError $exc) {
//            echo "\nErr " . $exc->getCode() . ' : '  . $exc->getMessage() ;
//            return json_encode(array('error' => 'Sorry, but either you or the code was not recognised')) ;
            return false ;

        } catch (\RateLimitError $exc) {
            return false ;

        } catch (\ApiKeyError $exc) {
            return false ;

        } catch (\ParameterError $exc) {
            return false ;

        } catch (\ConflictError $exc) {
            return false ;

        }

        return false ;              // Should never get here
    }


    /**
     * SET PASSWORD
     * ============
     * @return array
     */
    public function actionSetPassword()
    {
        $errors = [] ;

        // -------------------------------------------------------------------
        // Initialise for JSON returns
        // -------------------------------------------------------------------
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = 'json';
        }

        $viewname = 'reset' ;

        // -------------------------------------------------------------------
        // Fetch the key, initially posted by the caller (actionConfirm) and
        // subsequently as part of the form submission
        // -------------------------------------------------------------------
        if (!($userKey = Yii::$app->session->getFlash('__set_pw__'))) {
            //$userKey = array_key_exists('key', $_POST) ? $_POST['key'] : 0 ;
            if (Yii::$app->request->post('key')) {
                $userKey = Yii::$app->request->post('key');
            } elseif (Yii::$app->request->get('key')) {
                $userKey = Yii::$app->request->get('key');
            } else {
                $userKey = 0;
            }
        }

        if (!($user = $this->findIfPending($userKey))) {
            Yii::$app->session->setFlash('error', 'Invalid password request');
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = 'json';

                return ['error' => 'Invalid request'] ;
            }
            return Yii::$app->getResponse()->redirect('/', 200);
        }

        // -------------------------------------------------------------------
        // Validate the password details
        // -------------------------------------------------------------------
        if (Yii::$app->request->isPost) {
            $errors = $this->validatePasswordReset($user) ;

            // ---------------------------------------------------------------
            // If this was an ajax request, check for errors first.
            // If none, and the input contains a password, we can save
            // it and then create the GaUser.
            // ---------------------------------------------------------------
            if (Yii::$app->request->isAjax && count($errors) > 0) {
                return $errors ;
            }

            // ---------------------------------------------------------------
            // ---------------------------------------------------------------
            if (count($errors) == 0 &&
                ($error = $this->saveNewpassword($user)) === true) {

                $success = $this->createGauthifyUser($user) ;

                if (Yii::$app->request->isAjax) {
                    if ($success == true) {
                        $this->consumePendingKey($userKey);

                        $this->sendOutstandingPurchaseNotifications($user) ;

                        $result = [
                            'ok'    => true,
                            'ga'    => [
                                'qrurl'     => $this->gaDetails['qr_url'],
                                'ezurl'     => $this->gaDetails['ez_url'],
                                'account'   => $this->gaDetails['display_text'],
                                'key'       => $this->gaDetails['key'],
                                'usegauthify' => Yii::$app->params['useGauthify'] // tells JS if we should skip GAuthify step.
                            ]
                        ];

                        // ---------------------------------------------------
                        // Allow 3 requests to refresh the QR code etc
                        // ---------------------------------------------------
                        Yii::$app->session->set('__new_pw__', [1, $this->gaDetails['display_text'], $user->id, time()]) ;

                        return $result ;

                    } else {
                        return $success ;           // Must be an array of messages
                    }
                }
            }
        } // if isPost

        $ga = new \stdClass() ;
        $ga->qrUrl = '' ;
        $ga->ezUrl = '' ;
        $ga->account = '' ;
        $ga->key = '' ;

        // render
        return $this->render($viewname, [
            "userKey"   => $userKey,
            'user'      => $user,
            'ga'        => $ga,
            "success"   => $user !== false,
            'usegauthify' => Yii::$app->params['useGauthify'] // tells JS if we should skip GAuthify step.
        ]);

    }

    /**
     * SEND OUTSTANDING PURCHASE NOTIFICATIONS
     * =======================================
     * Called after a user creates their password and so confirm their account,
     * this method scans any recorded stockItems for entries which haven't yet
     * been notified to the user, then sends the email
     *
     * @param $user
     */
    private function sendOutstandingPurchaseNotifications($user) {

        $stockRooms = $user->stockrooms ;
        if (count($stockRooms)) {
            $srIds = [] ;
            foreach ($stockRooms as $stockRoom) {
                $srIds[] = $stockRoom->id ;
            }

            $stockItems = StockItem::find()
                            ->where(['send_email' => StockItem::EMAIL_SEND])
                            ->andWhere(['stockroom_id' => $srIds])
                            ->orderBy('stockroom_id')
                            ->all();

            if (count($stockItems)) {
                $stockitememailer = new StockItemEmailer();
                $stockitememailer->notifyCustomerofNewStockItems($stockItems, false);
            }
        }
    }
    
    /**
     * Account
     */
    public function actionAccount()
    {
        /** @var \amnah\yii2\user\models\User $user */
        /** @var \amnah\yii2\user\models\UserKey $userKey */
        
        // set up user and load post data
        $user = Yii::$app->user->identity;
        $user->setScenario("account");
        $loadedPost = $user->load(Yii::$app->request->post());

        // validate for ajax request
       /* if ($loadedPost && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($user);
        }*/

        // validate for normal request
        if ($loadedPost && $user->validate()) {
            
            // save, set flash, and refresh page
            $user->save(false);
            Yii::$app->session->setFlash("Account-success", Yii::t("user", "Account updated"));
            return $this->refresh();
        }

        // render
        return $this->render("account", [
            'user' => $user,
        ]);
    }

    /**
     * ACTION RELOAD GA
     * ================
     */
    public function actionReloadGa() {
        if (!Yii::$app->request->isAjax) {
            \Yii::$app->session->setFlash('error', 'Not authorised');

            return Yii::$app->getResponse()->redirect('/', 200);
        }

        Yii::$app->response->format = 'json';

        if (($validRequest = Yii::$app->session->get('__new_pw__'))) {

            $username       = Yii::$app->request->get('username');
            $remainingCount = $validRequest[0]--;
            $startTime      = $validRequest[3];

            // ---------------------------------------------------------------
            // Check there are still valid retries left, that the total time
            // elapsed doesn't exceed our maximum, that the request is for
            // the expected GA-Username, and then that the user account exists
            // on this machine
            // ---------------------------------------------------------------
            if ((time() - $startTime) < self::MAX_LIFE_FOR_SETPASSWORD &&
                $username == $validRequest[1] &&
                ($user = GAUser::findOne($validRequest[2])) ) {

                Yii::$app->session->set('__new_pw__', $validRequest) ;

                $user = GAUser::findOne($validRequest[2]);

                if ($user && $this->getGADetailsForUser($user) !== false) {
                    $result = [
                        'ok'    => true,
                        'ga'    => [
                            'qrurl'     => $this->gaDetails['qr_url'],
                            'ezurl'     => $this->gaDetails['ez_url'],
                            'account'   => $this->gaDetails['display_text'],
                            'key'       => $this->gaDetails['key'],
                            'usegauthify' => Yii::$app->params['useGauthify'] // tells JS if we should skip GAuthify step.
                        ]
                    ];

                    return $result ;
                }

            }
        }
        \Yii::$app->session->remove('__new_pw__') ;
        return ['error' => 'The reset token has expired'] ;
    }

    /**
     * VALIDATE PASSWORD RESET
     * =======================
     * @param $user
     *
     * @return array
     */
    private function validatePasswordReset($user) {
        $user->load(Yii::$app->request->post());
        $errors = ActiveForm::validate($user);

        // ---------------------------------------------------------------
        // Extra check as this isn't validating at the moment
        // ---------------------------------------------------------------
        if ($user->newPassword == '') {
            $errors['user-newpassword'][] = "New Password cannot be blank." ;
        }
        return $errors ;
    }

    /**
     * CONFIRM
     * =======
     * This method handles user signup confirmation using the link in the
     * registration email.
     *
     * The first time we get here we expect the supplied key to still be
     * valid (not consumed). If so, we validate the user, by flagging it
     * as consumed
     *
     */
    public function actionConfirm($key)
    {
        //$testToken = '1010101'; // allows us to develop the view for this page.

        // search for userKey
        if (($user = $this->validateKey($key) /* || $key==$testToken */)) {

            //if ($key==$testToken) {
            //    $user = GAUser::findOne(['role_id'=>2]); // grab any user for testing
            //}

            $userKey = Yii::$app->getModule("user")->model("UserKey");
            $userKey = $userKey::generate($user->id, $userKey::TYPE_GAPENDING);

            Yii::$app->session->setFlash('__set_pw__', $userKey->key) ;

            return Yii::$app->getResponse()->redirect('/gauth/set-password', 200);
        }

        return Yii::$app->getResponse()->redirect('/gauth/resend', 200);
    }

    /**
     * SAVE NEW PASSWORD
     * =================
     * @param $user
     *
     * @return bool
     */
    private function saveNewpassword($user)
    {
        $user->setScenario("reset");
        if (!$user->saveWithAuditTrail('Password updated from ip ' . Yii::$app->getRequest()->getUserIP())) {
            return $user->errors ;
        } else {
            $user->status = GAUser::STATUS_ACTIVE; // RCH 20150108
            if (!$user->saveWithAuditTrail('User set to ACTIVE')) {
                return $user->errors ;
            }
        }
        return true ;
    }

    /**
     * CREATE GAUTHIFY USER
     * ====================
     *
     * @param $user
     *
     * @return bool
     */
    private function createGauthifyUser($user) {
        $errors = [] ;

        // is gauthify support enabled?
        if (Yii::$app->params['useGauthify']) {

            $gauthify = $this->getGauth() ;
            try {
                $this->gaDetails = $gauthify->create_user($user->uuid, $user->email, $user->email);

                if ($this->gaDetails) {
                    $this->sendGaDetailsEmail($user, $this->gaDetails) ;
                }

            } catch (\NotFoundError $exc) {
                Yii::error($exc->getCode() . ' : '  . $exc->getMessage());
            //            echo "\nErr " . $exc->getCode() . ' : '  . $exc->getMessage() ;
            //            return json_encode(array('error' => 'Sorry, but either you or the code was not recognised')) ;
                return false ;

            } catch (\RateLimitError $exc) {
                Yii::error($exc->getCode() . ' : '  . $exc->getMessage());
                echo 'Rate ' ;
                print_r($exc) ;
                return false ;

            } catch (\ApiKeyError $exc) {
                Yii::error($exc->getCode() . ' : '  . $exc->getMessage());
                echo 'Key ' ;
                print_r($exc) ;
                return false ;

            } catch (\ParameterError $exc) {
                Yii::error($exc->getCode() . ' : '  . $exc->getMessage());
                echo 'Param  ' ;
                print_r($exc) ;
                return false ;

            } catch (\ConflictError $exc) {
                Yii::error($exc->getCode() . ' : '  . $exc->getMessage());
                $errors = $this->updateGauthify($user) ;

            } catch (\Exception $exc) {
                Yii::error($exc->getCode() . ' : '  . $exc->getMessage());
                print_r($exc) ;
            }

        }
        return count($errors) ? $errors : true ;
    }

    /**
     * UPDATE GAUTHIFY
     * ===============
     * Essentially the same as create user, except we can't change the display_name
     *
     * @param $user
     *
     * @return array|bool
     */
    private function updateGauthify ($user) {
        $errors = [] ;

        $gauthify = $this->getGauth() ;
        try {
            $this->gaDetails = $gauthify->update_user($user->uuid, $user->email);

            if ($this->gaDetails) {
                $this->sendGaDetailsEmail($user, $this->gaDetails) ;
            }

        } catch (\NotFoundError $exc) {
            //            echo "\nErr " . $exc->getCode() . ' : '  . $exc->getMessage() ;
            //            return json_encode(array('error' => 'Sorry, but either you or the code was not recognised')) ;
            return false ;

        } catch (\RateLimitError $exc) {
            echo 'Rate ' ;
            print_r($exc) ;
            return false ;

        } catch (\ApiKeyError $exc) {
            echo 'Key ' ;
            print_r($exc) ;
            return false ;

        } catch (\ParameterError $exc) {
            echo 'Param  ' ;
            print_r($exc) ;
            return false ;

        } catch (\ConflictError $exc) {
            $errors[] = 'Your account already exists on Google Authenticator' ;

        } catch (\Exception $exc) {
            print_r($exc) ;
        }

        return count($errors) ? $errors : true ;
    }

    /**
     * GET GA DETAILS FOR USER
     * =======================
     *
     * @param $user
     *
     * @return bool
     */
    private function getGADetailsForUser ($user) {
        $gauthify = $this->getGauth() ;
        try {
            $this->gaDetails = $gauthify->get_user($user->uuid);

            return $this->gaDetails ;

        } catch (\Exception $exc) {
            print_r($exc) ;
        }
        return false ;
    }

    /**
     * SEND GA DETAILS EMAIL
     * =====================
     * @param $user
     * @param $gaDetails
     *
     * @return mixed
     */
    private function sendGaDetailsEmail ($user, $gaDetails) {

         if (!Yii::$app->params['useGauthify']) {
            return TRUE; // not using Gauthify, so pretend all is okay
        }

        // calculate expireTime (converting via strtotime)
        $expireTime = Yii::$app->getModule("user")->resetKeyExpiration;
        $expireTime = $expireTime !== null ? date("Y-m-d H:i:s", strtotime("+" . $expireTime)) : null;

        // create userKey
        $userKey    = Yii::$app->getModule("user")->model("UserKey");
        $userKey    = $userKey::generate($user->id, $userKey::TYPE_PASSWORD_RESET, $expireTime);

        // modify view path to module views
        $mailer           = Yii::$app->mailer;
        $oldViewPath      = $mailer->viewPath;
        $mailer->viewPath = Yii::$app->getModule("user")->emailViewPath;

        // send email
        $subject = 'Exertis Digital Stock Room: ' . Yii::t("user", "Google Authenticator Setup");
        $message  = $mailer->compose('gadetailsEmail', compact("subject", "user", "userKey", 'gaDetails'))
                                ->setTo($user->email)
                                ->setSubject($subject);

        // check for messageConfig before sending (for backwards-compatible purposes)
        if (empty($mailer->messageConfig["from"])) {
            $message->setFrom(Yii::$app->params["adminEmail"]);
        }
        $result = $message->send();

        // restore view path and return result
        $mailer->viewPath = $oldViewPath;
        return $result;
    }



    /**
     * RESEND
     * ======
     * Resend email confirmation
     */
    public function actionResend()
    {
        // load post data and send email

        $model = Yii::$app->getModule("user")->model("ResendForm");
        if ($model->load(Yii::$app->request->post()) && $model->sendEmail()) {

            // set flash (which will show on the current page)
            Yii::$app->session->setFlash("Resend-success", Yii::t("user", "Confirmation email resent"));

            $viewname = 'confirmError' ;
            $model = Yii::$app->getModule("user")->model("ResendForm");

        } else {
            $viewname = 'confirmError' ;
            $model = Yii::$app->getModule("user")->model("ResendForm");
        }

        // render
        return $this->render($viewname, [
            "user" => $model,
        ]);
    }


    /**
     * VALIDATE KEY
     * ===========
     * Checks the user key. Almost a direct copy of the code from Yii1-user,
     * except this returns the user object on success
     *
     * @param $key
     *
     * @return bool
     */
    private function validateKey ($key, $keyType=null) {

        $success = false;

        $userKey = Yii::$app->getModule("user")->model("UserKey");

        // -------------------------------------------------------------------
        // Now we have a reference to the key type, we can set defaults for
        // the expected type, if not given
        // -------------------------------------------------------------------
        if (!$keyType) {
            $keyType = [$userKey::TYPE_EMAIL_ACTIVATE, $userKey::TYPE_EMAIL_CHANGE] ;
        }

        // -------------------------------------------------------------------
        // Search for the requested key and if found flag it as consumed
        // -------------------------------------------------------------------
        $userKey = $userKey::findActiveByKey($key, $keyType);

        if ($userKey) {
            $userKey->consume();

            $user = Yii::$app->getModule("user")->model("User");
            $user = $user::findOne($userKey->user_id);
            $success = $user ;          // Returns the user object
        }

        return $success ;
    }


    /**
     * CONSUME PENDING KEY
     * ===================
     * @param $key
     *
     * @return bool
     */
    private function consumePendingKey($key) {
        $success = false;

        if ($key) {
            $userKey = Yii::$app->getModule("user")->model("UserKey");
            // -------------------------------------------------------------------
            // Search for the requested key and if found flag it as consumed
            // -------------------------------------------------------------------
            $userKey = $userKey::findActiveByKey($key, $userKey::TYPE_GAPENDING);
            if ($userKey) {
                $userKey->consume();
                $success =  true  ;
            }
        }
        return $success ;
    }

    /**
     * FIND IF PENDING
     * ===============
     *
     * @param string $key key on the URL
     * @param string $keyType Defaults to GAUserKey::TYPE_GAPENDING, or use UserKey::TYPE_PASSWORD_RESET
     *
     * @return bool
     */
    private function findIfPending($key) {

        $success = false;

        if ($key) {
            $userKeyModel = Yii::$app->getModule("user")->model("UserKey");
            // -------------------------------------------------------------------
            // Search for the requested key and if found flag it as consumed
            // -------------------------------------------------------------------
            $userKey = $userKeyModel::findActiveByKey($key, $userKeyModel::TYPE_GAPENDING);
            if ($userKey) {
                $userModel    = Yii::$app->getModule("user")->model("User");
                $success = $userModel::findOne($userKey->user_id);
            } else {
                // RCH Also check for TYPE_PASSWORD_RESET
                $userKey = $userKeyModel::findActiveByKey($key, $userKeyModel::TYPE_PASSWORD_RESET);
                if ($userKey) {
                    $userModel    = Yii::$app->getModule("user")->model("User");
                    $success = $userModel::findOne($userKey->user_id);
                }
            }
        }
        return $success ;
    }


    /**
     * GET GAUTH
     * =========
     * Loads and returns an instance of the Gauthify module
     *
     * @return EDSRGAuthify
     */
    private function getGauth() {
        $authAPIKey = isset(Yii::$app->params['authify']) ? Yii::$app->params['authify'] : null ;

        return new EDSRGAuthify($authAPIKey) ;

    }

}
