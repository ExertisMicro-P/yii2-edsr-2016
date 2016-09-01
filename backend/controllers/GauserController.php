<?php

namespace backend\controllers;

use Yii;
use common\models\gauth\GAUser;
use common\models\gauth\GAUserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\filters\AccessControl;

/**
 * GAUserController implements the CRUD actions for GAUser model.
 */
class GauserController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],

			 'access' => [
                'class' => AccessControl::className(),
                //'only' => ['index', 'view', 'create','update', 'delete'],
                'rules' => [
                    [
                        'allow' => Yii::$app->user->can("admin"),
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'ajax-reset-password', 'ajax-resend-invitation'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all GAUser models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GAUserSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single GAUser model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        // Gather Audit Trail
        $audittrailSearchModel = new \common\models\AudittrailSearch();
        $audittrailDataProvider = $audittrailSearchModel->search(['AudittrailSearch' => ['table_name' => GAUser::tableName(), 'record_id'=>intval($id)]]);


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        return $this->redirect(['view', 'id' => $model->id]);
        } else {
        return $this->render('view',
                        [
                            'model' => $model,
                            'audittrailDataProvider' => $audittrailDataProvider,
                            'audittrailSearchModel' => $audittrailSearchModel,

                        ]
                );
}
    }

    /**
     * Creates a new GAUser model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GAUser;
        $model->setScenario('admin');
        $model->status = GAUser::STATUS_INACTIVE;

        if ($model->load(Yii::$app->request->post())) {

            //$model->validate(); // called here mainly to set teh defaults

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }  else {
                Yii::error(\yii\helpers\VarDumper::dump($model->getErrors(),10,true));
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing GAUser model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing GAUser model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the GAUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GAUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GAUser::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    
    
    /**
     * Sends a link to the given email address where the user can reset his/her password.
     * If different email address given, it will also change the user's email address.
     * 
     * @param string $email The email address where we send the email.
     * @param int $userId User's ID
     * @return JSON returns JSON to the AJAX
     */
    
    public function actionAjaxResetPassword(){
        $userId = Yii::$app->request->post('id');
        $email = Yii::$app->request->post('email');
        $audittrail = new \exertis\savewithaudittrail\models\Audittrail();
        $response = [];
        
        //$email = 'dominik.jaross@exertis.co.uk';
        //$userId = 10;
                
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['status'] = 400;
            $response['message'] = 'Incorrect email address.';
            
            echo json_encode($response);
            return;
        }
        
        $emailExists = GAUser::find()->where(['email' => $email])->andWhere(['<>', 'id', $userId])->orWhere(['new_email' => $email])->exists();
        
        if($emailExists){
            $response['status'] = 400;
            $response['message'] = 'This email address is already assigned to a user.';
            
            echo json_encode($response);
            return;
        }
        
        if($this->_sendForgotPassword($userId, $email)){
            $response['status'] = 200;
            $response['message'] = 'Password reset link successfully sent to ' . $email . '.';
            $audittrail->log($response['message'], GAUser::tableName(), $userId, Yii::$app->user->identity);
        } else {
            $response['status'] = 400;
            $response['message'] = 'There was an error while resetting the email.';
        }
        
        echo json_encode($response);
        return;
        
        
    }
    
    
    /**
     * Sends a link to the given email address where the user can confirm his/her email address and finalize the registration.
     * If different email address given, it will also change the user's email address.
     * 
     * @param string $email The email address where we send the email.
     * @param int $userId User's ID
     * @return JSON returns JSON to the AJAX
     */
    
    public function actionAjaxResendInvitation(){
        $userId = Yii::$app->request->post('id');
        $email = Yii::$app->request->post('email');
        $audittrail = new \exertis\savewithaudittrail\models\Audittrail();
        $response = [];
        
        //$userId = 5;
        //$email = 'dominik.jaross@exertis.co.uk';
                        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['status'] = 400;
            $response['message'] = 'Incorrect email address. (' . $email . ')';
            
            echo json_encode($response);
            return;
        }
        
        $emailExists = GAUser::find()->where(['email' => $email])->andWhere(['<>', 'id', $userId])->orWhere(['new_email' => $email])->exists();
        
        if($emailExists){
            $response['status'] = 400;
            $response['message'] = 'This email address is already assigned to a user.';
            
            echo json_encode($response);
            return;
        }
        
        if($this->_resendInvitation($userId, $email)){
            $response['status'] = 200;
            $response['message'] = 'Invitation email successfully sent to ' . $email . '.';
            $audittrail->log($response['message'], GAUser::tableName(), $userId, Yii::$app->user->identity);
        } else {
            $response['status'] = 400;
            $response['message'] = 'There was an error while resending the email.';
        }
        
        echo json_encode($response);
        return;
        
        
    }
    
    
    private function _sendForgotPassword($userId, $email){
        
        $user = GAUser::findOne(['id'=>$userId]);
        $user->setScenario('emailsetup');
        
        $user->status = 0;
        $user->email = $email;
        $user->save();
        
        
        $mailer           = Yii::$app->mailer;
        $oldViewPath      = $mailer->viewPath;
        $mailer->viewPath = Yii::$app->getModule("user")->emailViewPath;

        
        $userKey = Yii::$app->getModule("user")->model("UserKey");
        $userKey = $userKey::generate($userId, $userKey::TYPE_PASSWORD_RESET);
        $subject = 'Exertis Digital Stock Room: ' . Yii::t("user", "Password Reset");
        $message = $mailer->compose('forgotUserPassword', compact("subject", "userKey"))
            ->setTo($email)
            ->setBcc(Yii::$app->params['account.copyAllEmailsTo'])// RCH 20150420
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
    
    
    private function _resendInvitation($userId, $email){
        
        $user = GAUser::findOne(['id'=>$userId]);
        $user->setScenario('emailsetup');
        
        $user->status = 0;
        $user->email = $email;
        $user->save();
        
        
        $mailer           = Yii::$app->mailer;
        $oldViewPath      = $mailer->viewPath;
        $mailer->viewPath = Yii::$app->getModule("user")->emailViewPath;

        
        $userKey = Yii::$app->getModule("user")->model("UserKey");
        $userKey = $userKey::generate($userId, $userKey::TYPE_EMAIL_ACTIVATE);
        $subject = 'Exertis Digital Stock Room: ' . Yii::t("user", "Email Confirmation");
        $message = $mailer->compose('confirmEmail', compact("subject", "userKey", "user"))
            ->setTo($email)
            ->setBcc(Yii::$app->params['account.copyAllEmailsTo'])// RCH 20150420
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
    
    
}
