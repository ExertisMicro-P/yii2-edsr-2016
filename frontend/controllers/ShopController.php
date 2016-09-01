<?php
namespace frontend\controllers;

use common\models\DigitalProduct;
use common\models\StockActivity;
use common\models\StockItem;
use Yii;
use Url;
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
use common\components\CreditLevel ;
use common\models\DigitalProductSearch;

use frontend\models\RegisterForm;


/**
 * Site controller
 */
class ShopController extends SiteController
{

    
    
    /**
     * INDEX
     * =====
     *
     * @return \yii\web\Response
     */
    public function actionIndex()
    {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect('/user/index');
        }
        
        if(!Yii::$app->user->identity->shopEnabled){
            return $this->redirect('/');
        }
                
        $stockroom = \common\models\Stockroom::findOne(['account_id'=>Yii::$app->user->identity->account_id]);
        if (!empty($stockroom)) { // RCH 20160801
            $stockItems = $stockroom->stockItems;

            if(count($stockItems) >= 10000){
                $users = \common\models\gauth\GAUser::findAll(['account_id'=>Yii::$app->user->identity->account_id]);

                foreach($users as $usr){
                    $usr->shopEnabled = 0;
                    $usr->save(false);
                }
            }

            return $this->_listItems();
        } else {
            // RCH 20160801
            \Yii::error(__METHOD__.': Problem fetching stock room for Account ID '.Yii::$app->user->identity->account_id);
        }
    }

    /**
     * LIST ITEMS
     * ==========
     * @return string
     */
    private function _listItems()
    {
        $this->getUserDetails();
        $orders = $this->getDigitalProductsForDisplay();

        $cLevel = new CreditLevel($this->user) ;

        $bodyContent = $this->renderPartial('/yiicomp/shop/itemtable-html', [
            'title'        => 'Shop Items',
            'dataProvider' => $orders['provider'],
            'searchModel'  => $orders['model'],
            'canBuy'       => $this->user->account->customer->status == \common\models\Customer::STATUS_TRANSACTIONAL,
            'isRetailView' => $this->user->account->use_retail_view, // RCH 20151002
            'credit'       => $cLevel->readCurrentCredit()
        ]);
//echo $bodyContent;exit;
        return $this->render('/site/customerHome', [
            'bodyContent' => $bodyContent
        ]);

    }

    /**
     * ACTiON DETAIL
     * =============
     * Returns details of a product, which, currently, will be displayed
     * using a bootstrap modal window.
     *
     * @return string
     */
    public function actionDetail () {
        $productId = Yii::$app->request->get('product', 0) ;


        $user = \Yii::$app->user->getIdentity() ;
        if (!$user || !$user->account) {
            return ;
        }
        $account = $user->account ;
        $accountNumber = $account->customer_exertis_account_number ;

        $digitalProduct = DigitalProduct::find()->where(['id' => $productId])->one() ;
        return $this->renderPartial('/yiicomp/shop/detail', [
            'product' => $digitalProduct,
            'price' => $digitalProduct->getItemPrice($accountNumber)
        ]);
    }


    /**
     * GET PRODUCTS FOR DISPLAY
     * ===============
     *      *
     * @return array|string
     */
    protected function getDigitalProductsForDisplay () {

        // -------------------------------------------------------------------
        // Add any user provided search criteria, then ensure the critical
        // details are overwritten to avoid the user attempting to access
        // other accounts.
        // -------------------------------------------------------------------
        $userInputs = Yii::$app->request->getQueryParams() ;
        if (array_key_exists('DigitalProductSearch', $userInputs)) {
            $params = $userInputs ;
        } else {
            $params = [] ;
        }

        // -------------------------------------------------------------------
        // -------------------------------------------------------------------
        $searchModel = new DigitalProductSearch;
        $dataProvider = $searchModel->searchShop($params);

        return ['model' => $searchModel, 'provider' => $dataProvider] ;

    }

}
