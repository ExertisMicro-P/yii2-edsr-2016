<?php

namespace backend\controllers;

use Yii;
use common\models\Account;
use common\models\AccountSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\filters\AccessControl;

use yii\web\UploadedFile;

/**
 * AccountController implements the CRUD actions for Account model.
 */
class AccountController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'ajax-manage-shop', 'ajax-set-test-user', 'ajax-set-test-user2'],
                        'roles' => ['@'],
                    ],
                ],
            ],        ];
    }

    /**
     * Lists all Account models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AccountSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Account model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        $stockroomsSearchModel = new \common\models\StockroomSearch();
        $stockroomsDataProvider = $stockroomsSearchModel->search(['StockroomSearch'=>['account_id'=>$id]]);

        $userSearchModel = new \common\models\gauth\GAUserSearch();
        $usersDataProvider = $userSearchModel->search(['GAUserSearch'=>['account_id'=>$id]]);

        $accountRuleMappingModelSearch = new \backend\models\AccountRuleMappingSearch();
        $accountRuleMappingDataProvider = $accountRuleMappingModelSearch->search(['account'=>$id]);

        // Gather Audit Trail
        $audittrailSearchModel = new \common\models\AudittrailSearch();
        $audittrailDataProvider = $audittrailSearchModel->search(['AudittrailSearch' => ['table_name' => Account::tableName(), 'record_id'=>intval($id)]]);
        
        $companyLogo = '';
        
        if(!$model->getAccountLogo()){
            
            $mainUser = $model->findMainUser();
            
            if($mainUser){
                $companyEmail = $mainUser->email;
                $companyDomain = explode('@', $companyEmail)[1];
                $companyLogo = 'http://logo.clearbit.com/'.$companyDomain;

                if(!\common\components\LogoHelper::cURLImage($companyLogo)){
                    $companyLogo = 'http://stockroomdev.exertis.co.uk/img/no-boxshot.jpg';
                }
            } else {
                $companyLogo = 'http://stockroom.exertis.co.uk/img/no-boxshot.jpg';
            }
        
        }

        if ($model->load(Yii::$app->request->post())) {

            // get the uploaded file instance. for multiple file uploads
            // the following data will return an array
            $image = UploadedFile::getInstance($model, 'image');

            if (!empty($image)) {
             // store the source file name
                $model->image = $image->name;
                $ext = end((explode(".", $image->name)));
            

                // generate a unique file name
                $model->logo = Yii::$app->security->generateRandomString().".{$ext}";

                // the path to save file, you can set an uploadPath
                // in Yii::$app->params (as used in example below)
                $path = Yii::$app->params['uploadPath'] .'account_logos/'. $model->logo;
            }

            if($model->save()){
                 if (!empty($image)) {
                     $image->saveAs($path);
                 }
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                // error in saving model
                throw new \yii\web\HttpException('Couldn\'t save: '.\yii\helpers\VarDumper::dumpAsString($model->getErrors(),99,true));
            }
        } else {
        return $this->render('view',
                                ['model' => $model,
                                 'companyLogo' => $companyLogo,
                                 'stockroomsDataProvider'=>$stockroomsDataProvider,
                                 'usersDataProvider'=>$usersDataProvider,
                                 'accountRuleMappingDataProvider'=>$accountRuleMappingDataProvider,
                                 'audittrailDataProvider' => $audittrailDataProvider,
                                 'audittrailSearchModel' => $audittrailSearchModel,
                            ]);
}
    }

    /**
     * Creates a new Account model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Account;

        if ($model->load(Yii::$app->request->post())) {

            // get the uploaded file instance. for multiple file uploads
            // the following data will return an array
            $image = UploadedFile::getInstance($model, 'image');

             // store the source file name
            $model->filename = $image->name;
            $ext = end((explode(".", $image->name)));

            // generate a unique file name
            $model->logo = Yii::$app->security->generateRandomString().".{$ext}";

            // the path to save file, you can set an uploadPath
            // in Yii::$app->params (as used in example below)
            $path = Yii::$app->params['uploadPath'] . $model->avatar;

            if($model->save()){
                $image->saveAs($path);
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                // error in saving model
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Account model.
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
     * Deletes an existing Account model.
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
     * Finds the Account model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Account the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Account::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested Account does not exist.');
        }
    }  
    
    
    public function resendSignUpEmail($id) {
        $account = $this->findModel($id);
        $mainUser = $account->findMainUser();
        $email = $mainUser->email;
        $email = 'russell.hutson@exertis.co.uk'; // for testing
        
        $model = Yii::$app->getModule("user")->model("ResendForm");
        $model->email = $email; // set the email address to send the reminder to
        if ($model->validate() && $model->sendEmail()) {
            $account->saveWithAuditTrail(__METHOD__.': Email confirmation resent');
            Yii::$app->getSession()->setFlash('success', 'Email notification sent');
        }
    }
    
    //Ajax call from enableDisable.js
    public function actionAjaxManageShop($accountId, $action){
                    
        if($action === 'enable'){
            
            $query = \common\models\gauth\GAUser::updateAll(['shopEnabled' => 1], 'account_id = '.$accountId.'');
            
            if($query){
                $res = true;
            } else {
                $res = false;
            }
            
        }
        
        elseif($action === 'disable'){
            
            $query = \common\models\gauth\GAUser::updateAll(['shopEnabled' => 0], 'account_id = '.$accountId.'');
            
            if($query){
                $res = true;
            } else {
                $res = false;
            }
            
        }
        
        return $res;
        
    }
    
      
    //Ajax call from enableDisable.js
    public function actionAjaxSetTestUser($user, $account){
        
        $userId = \common\models\gauth\GAUser::findOne(['email'=>$user])->id;
        $query = \common\models\gauth\GAUser::updateAll(['account_id' => $account], 'id = ' . $userId);
        
        if($query){
            echo '<b style="color:green">'.$user.' set to this account!</b>';
        } else {
            echo '<b style="color:red">Error while saving.</b>';
        }
        
    }
}
