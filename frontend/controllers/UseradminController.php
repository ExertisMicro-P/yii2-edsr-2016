<?php

namespace frontend\controllers;

use Yii;
use app\models\Useradmin;
use app\models\UseradminSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UseradminController implements the CRUD actions for Useradmin model.
 */
class UseradminController extends Controller
{
    
    public $accountId;
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
    
    public function beforeAction($action) {
        if (Yii::$app->user->isGuest) {
            $this->redirect('/site/index');
        }
        
        $this->accountId = Yii::$app->user->identity->account_id;
        return parent::beforeAction($action);
    }

    /**
     * Lists all Useradmin models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            $this->redirect('/site/index');
        }
        
        $searchModel = new UseradminSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Useradmin model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (\Yii::$app->user->isGuest) {
            $this->redirect('/site/index');
        }
        
        
            $this->redirect('/useradmin');
    }

    /**
     * Creates a new Useradmin model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (\Yii::$app->user->isGuest) {
            $this->redirect('/site/index');
        }
        
        Yii::info(__METHOD__);
        
        $newuser = new \common\models\gauth\GAUser();
        $newuser->setScenario('emailsetup');
        $newuser->account_id = $this->accountId;
        
        if  (Yii::$app->request->isPost) {
            $loadOkay = $newuser->load(Yii::$app->request->post());
            $newuser->username = preg_replace('/[^\w]/','_', $newuser->email);
            $newuser->setScenario('emailsetup');
            
            if ($loadOkay && $newuser->save()) {
                Yii::info(__METHOD__.': Added '.print_r($newuser->attributes,true));
                
                $user = \common\models\gauth\GAUser::findOne($newuser->id);

                $userKey = Yii::$app->getModule("user")->model("UserKey");
                $userKey = $userKey::generate($newuser->id, $userKey::TYPE_PASSWORD_RESET);
                $subject = 'EDSR Invitation';

                $message = Yii::$app->mailer->compose('confirmEmail', ['subject' => $subject, 'user'=>$user, 'userKey' => $userKey])
                    ->setFrom('webteam@exertis.co.uk')
                    ->setTo($newuser->email)
                    ->setSubject($subject)
                    ->send();
                
                $notifyWebteam = Yii::$app->mailer->compose('newUserByCustomerAdmin', ['user'=>$user])
                    ->setFrom('webteam@exertis.co.uk')
                    ->setTo('webteam@exertis.co.uk')
                    ->setSubject('EDSR: New user has been added.')
                    ->send();
                
                $this->redirect('/useradmin');
            } else {
                Yii::error(__METHOD__.': Error saving '.print_r($newuser->attributes,true).' / '.print_r($newuser->getErrors(),true));
            }
        } else {
            return $this->render('create', [
                'model' => $newuser,
            ]);
        }
    }
    
    public function actionViewuser($id){
        
        if (\Yii::$app->user->isGuest) {
            $this->redirect('/site/index');
        }
        
        $model = \common\models\gauth\GAUser::find()->where(['id'=>$id])->one();
        
        if($model->account_id != $this->accountId) {
            $this->redirect('/useradmin');
        }
        
        $activity = \exertis\savewithaudittrail\models\Audittrail::find()->where(['username'=>$model->email]);
        
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $activity,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);
        
        
        return $this->render('userinfo', ['model'=>$model, 'dataProvider' => $dataProvider]);
        
    }

    /**
     * Updates an existing Useradmin model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (\Yii::$app->user->isGuest) {
            $this->redirect('/site/index');
        }
        
        $model = \common\models\gauth\GAUser::findById($id);
        
        if($model->account_id != $this->accountId) {
            $this->redirect('/useradmin');
        }
        
        $model->setScenario('edituser');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->redirect('/useradmin');
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the Useradmin model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Useradmin the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Useradmin::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    
    /**
     * Changes the user's status
     * @param integer $userId
     * @param integer $status
    */
    public function actionEnableordisable($userId, $status){
        
        //Enable User
        if($status === 'disabled') {
            
            $user = \common\models\gauth\GAUser::find()->where(['id'=>$userId])->one();
            
            $user->setScenario('edituser');
            
            if($user->password == NULL){
                echo 'The user\'s password is not set yet.';
                return;
            }
            
            $user->status = 1;
            if($user->save()){
                echo 'User has been enabled.';
            } else {
                echo print_r($user);
            }
            
        }
        
        //Disable User
        elseif($status === 'enabled') {
            
            $user = \common\models\gauth\GAUser::find()->where(['id'=>$userId])->one();
            
            $user->setScenario('edituser');
            
            $user->status = 0;
            if($user->save()){
                echo 'User has been disabled.';
            } else {
                echo print_r($user);
            }
            
        }
        
        
    }
    
    
    /**
     * Changes the shop's status for the user
     * @param integer $userId
     * @param integer $status
    */
    public function actionEnableordisableshop($userId, $status){
        
        //Enable Shop
        if($status === 'disabled') {
            
            $user = \common\models\gauth\GAUser::find()->where(['id'=>$userId])->one();
            $account = \common\models\Account::find()->where(['id'=>$user->account_id])->one();
            $mainUser = $account->findMainUser();
                        
            //\yii\helpers\VarDumper::dump($account, 99, true); die();
            
            $user->setScenario('edituser');
            
            if($mainUser->shopEnabled == false){
                echo 'Shop cannot be enabled until the main user\'s shop is disabled.';
                return;
            }
            
            if($user->password == NULL){
                echo 'The user\'s password is not set yet.';
                return;
            }
            
            $user->shopEnabled = 1;
            if($user->save()){
                echo 'Shop has been enabled for this user.';
            } else {
                echo print_r($user);
            }
            
        }
        
        //Disable Shop
        elseif($status === 'enabled') {
            
            $user = \common\models\gauth\GAUser::find()->where(['id'=>$userId])->one();
            
            $user->setScenario('edituser');
            
            $user->shopEnabled = 0;
            if($user->save()){
                echo 'Shop has been disabled for this user.';
            } else {
                echo print_r($user);
            }
            
        }
        
        
    }
    
    
    public function actionResend($id){
        
        $user = \common\models\gauth\GAUser::findOne($id);
        
        $userKey = Yii::$app->getModule("user")->model("UserKey");
        $userKey = $userKey::generate($id, $userKey::TYPE_PASSWORD_RESET);
        $subject = 'EDSR Invitation';
        
        $message = Yii::$app->mailer->compose('confirmEmail', ['subject' => $subject, 'user' => $user, 'userKey' => $userKey])
            ->setFrom('webteam@exertis.co.uk')
            ->setTo($user->email)
            ->setSubject($subject)
            ->send();
        
        
        if($message){
            Yii::$app->session->setFlash('success', 'Invitation has been sent successfully.');
            $this->redirect('/useradmin');
        } else {
            Yii::$app->session->setFlash('danger', 'Invitation could not be sent.');
            $this->redirect('/useradmin');
        }
        
    }
    
    
}
