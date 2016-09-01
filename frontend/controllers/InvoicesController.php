<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Account;
use common\models\InvoiceDetail;

class InvoicesController extends EdsrController
{
    public $layout = '@frontend/views/layouts/mainnw';

    /**
     * INDEX
     * =====
     */
    public function actionIndex () {
        set_time_limit(60);
        if($this->getUserAccount()){
         
        $searchModel = new \common\models\InvoiceHeaderSearch();
        $invoiceHeaderProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', ['invoiceHeaderProvider' => $invoiceHeaderProvider, 'searchModel'=>$searchModel]);
            
        }        
    }
    
    public function actionView($id){
        if($this->getUserAccount()){
            $invoiceHeader = \common\models\InvoiceHeader::findOne(['ih_header_row_id' => $id]);
            
            if($this->getUserAccount()->customer_exertis_account_number != $invoiceHeader->ih_account_number){
                return $this->redirect('/invoices/index');
            }

            $searchModel = new \common\models\InvoiceDetailSearch();
            $invoiceDetailProvider = $searchModel->search(Yii::$app->request->queryParams, ['invoiceno'=>$invoiceHeader->ih_invoice_number]);


            return $this->render('view', ['invoiceHeader'=>$invoiceHeader, 'invoiceDetailProvider' => $invoiceDetailProvider, 'searchModel'=>$searchModel]);
        }
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

        return $account;

    }

}
