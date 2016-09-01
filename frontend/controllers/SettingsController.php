<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Account ;
use yii\web\UploadedFile;

class SettingsController extends EdsrController
{
    public $layout = '@frontend/views/layouts/mainnw';

    /**
     * INDEX
     * =====
     */
    public function actionIndex () {
        if (($account = $this->getUserAccount())) {
                
            $userActivity = $account->getUserAuditTrailEntries();
            $mainUser = $account->findMainUser();
            
            
            if (Yii::$app->request->isPost &&
                $account->load(Yii::$app->request->post())) {

                $connection  = Account::getDb();
                $transaction = $connection->beginTransaction();
                $errmsg =  '' ;
                
                // -----------------------------------------------------------
                // If either the image save or the detail save fails, issue a
                // flash error messages. Otherwise return success
                // -----------------------------------------------------------
                if ($this->saveLogo($account)) {
                    if ($account->save()) {
                        $transaction->commit();

                        \Yii::$app->session->setFlash('success', 'Your changes have been recorded');
                        return $this->render('index', ['account' => $account, 'userActivity'=>$userActivity, 'mainUser' => $mainUser]);

                    } else {
                        $errmsg = 'Unable to save your changes' ;
                    }
                } else {
                    $errmsg = 'Failed to save the new logo' ;
                }
                $transaction->rollBack();
                \Yii::$app->session->setFlash('error', $errmsg);
            }
            
            /*$activity = \exertis\savewithaudittrail\models\Audittrail::find()->where(['record_id'=>$account->id])->orderBy('timestamp DESC');
        
            $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => $activity,
                'pagination' => [
                    'pageSize' => 7,
                ]
            ]);*/
            
            // either the page is initially displayed or there is some validation error
            return $this->render('index', ['account' => $account, 'userActivity'=>$userActivity, 'mainUser' => $mainUser]);
        }
    }

    /**
     * SAVE LOGO
     * =========
     * @param $account
     *
     * @return mixed
     * @throws \yii\web\HttpException
     *
     */
    private function saveLogo($account) {
        $result = true ;
        
        // get the uploaded file instance. for multiple file uploads
        // the following data will return an array
        $image = UploadedFile::getInstance($account, 'logo');
        if ($image) {
            
            $cloudinary = new \common\components\Cloudinary\Cloudinary();
            $cloudinaryUploader = new \common\components\Cloudinary\Uploader();

            $cloudinary->config(array( 
                "cloud_name" => "exertis-uk", 
                "api_key" => "891175815496819", 
                "api_secret" => "1EJAZBURnJ0QW3bvbVhHYi843Qs" 
              ));
            
            $accNo = Account::findOne(['id' => Yii::$app->user->identity->account_id])['customer_exertis_account_number'];
            //\yii\helpers\VarDumper::dump($image, 99, true); die();
            
            // -------------------------------------------------------------------
            // First, grab the current logo image details. Se need to delete it
            // if the new one fails to save
            // -------------------------------------------------------------------
            $currentLogo =  Yii::getAlias('@webroot') . '/' . $account->logo ;

            // -------------------------------------------------------------------
            // generate a unique file name, keeping the extension and record it
            // in the account object
            // -------------------------------------------------------------------
            $ext = strrchr($image->name, '.') ;
            $account->logo = $accNo . '-logo.jpg' ;

            // -------------------------------------------------------------------
            // now save the file in the designated logo directory
            // -------------------------------------------------------------------
            $path     = Yii::$app->params['uploadPath'] . 'account_logos/' . $account->logo;
            $fullPath = Yii::getAlias('@webroot') . '/' . $path;

            if ($result = $image->saveAs($fullPath)) {
                if (file_exists($currentLogo) && is_file($currentLogo)) {
                    unlink($currentLogo);
                }

                if(!$cloudinaryUploader->upload($fullPath, ['public_id'=>$accNo.'-logo', 'folder'=>'edsr/account_logos/'])) {
                    $result = false;
                } 
                
            }
        }
        return $result ;
    }


    /**
     * GET USER ACCOUNT
     * ================
     * If the user is invalid, this redirects to the home page and return false ;
     */
    private function getUserAccount ()
    {

        $this->user = \Yii::$app->user->getIdentity();
        if (!$this->user ||
            !$this->user->can('add_customer_user')) {
            $this->redirect('/');
            return false ;
        }
        $account = Account::findOne($this->user->account_id);

        return $account ; ;

    }

}
