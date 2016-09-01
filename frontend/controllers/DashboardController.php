<?php
namespace frontend\controllers;

use common\models\DigitalProduct;
use common\models\EmailedItem;
use common\models\EmailedUser;
use common\models\Orderdetails;
use common\models\SalesRepOrder;
use common\models\SalesRepOrderSearch;
use common\models\SessionOrder;

use common\models\StockItem;
use Yii;
use yii\web\Controller;

use yii\filters\AccessControl;
use common\components\DigitalPurchaser;

use exertis\savewithaudittrail\models\Audittrail;
use common\components\CreditLevel;
use common\models\Accounts;
use yii\data\ActiveDataProvider;
use common\models\SessionOrderSearch;
use common\models\SalesRepSearch;

use common\models\gauth\GAUser;

;
use common\models\Account;
use common\models\AccountSearch;


class DashboardController extends EdsrController
{
    public $layout = '@frontend/views/layouts/mainnw';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'masquerade', 'masqueradecheck', 'recentorder'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * BEFORE ACTION
     * =============
     * This is used to clear the masquerade flag which is used in other
     * controllers to switch to the masqueraded account
     *
     * @param \yii\base\Action $event
     *
     * @return bool|\yii\web\Response
     */
    public function beforeAction($event)
    {
        Yii::$app->session->remove('masquerade_user');
        Yii::$app->session->remove('current_account');      // Don't know why I have both

        return parent::beforeAction($event);
    }

    /**
     * INDEX
     * =====
     * The main entry point
     */
    public function actionIndex()
    {
        if (!Yii::$app->session->get('internal_user') ||
            !Yii::$app->user->can('buy_for_customer')
        ) {
            return $this->redirect('/');
        }

        $this->getUserDetails();

        $accountModel        = new AccountSearch;
        $accountDataProvider = $accountModel->search(Yii::$app->request->getQueryParams());

        $pendingData     = $this->getPendingProvider();
        $recentOrderData = $this->getRecentOrders();
//echo '<pre>' ;
//print_r($recentOrderData) ;exit;

        return $this->render('index', [
            'title'                   => 'Orders',
            'accountModel'            => $accountModel,
            'accountDataProvider'     => $accountDataProvider,

            'pendingSearchModel'      => $pendingData[0],
            'pendingDataProvider'     => $pendingData[1],

            'recentOrderSearchModel'  => $recentOrderData[0],
            'recentOrderDataProvider' => $recentOrderData[1]


        ]);
    }

    /**
     * RECENT ORDER
     * ============
     *
     */
    public function actionRecentorder()
    {
        $this->getUserDetails();

        $salesRepOrderKey = Yii::$app->request->post('expandRowKey', 0);

        $salesRepOrder = SalesRepOrder::find()
                                ->where(['id'           => $salesRepOrderKey,
                                         'sales_rep_id' => $this->user->id])
                                ->one();

        if ($salesRepOrder) {
            $po = $salesRepOrder->po ;

            $orderdItems = Orderdetails::find()
                                ->where(['po' => $po])
                                ->joinWith('stockitem')
                                ->joinWith('stockitem.digitalProduct')
                                ->all() ;
        }

        return $this->renderPartial('_recent_order', ['salesRepOrder' => $salesRepOrder, 'orderdItems' => $orderdItems]);

        echo $po;
    }

    /**
     * MASQUERADE CHECK
     * ================
     * This is called with the account number of the business that the rep
     * wants to act for. It finds details of all know, active, users on the
     * account and returns a response based on the number found;
     *
     *      0   It displays a form to allow new account to be added
     *      1   It shows the user details and asks for confirmation to masquerade
     *      >1  It lists all users and asks the rep to select one
     *
     * @param $id
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionMasqueradecheck($id)
    {
        if (!Yii::$app->user->can('buy_for_customer')) {
            throw new \yii\web\NotFoundHttpException;
        }

        $account = Account::find($id)->where(['id' => $id])->one();
        $users   = $account->getUsers()->where(['status' => 1])->all();

        switch (count($users)) {
            case 1:
                return $this->renderPartial('_confirm_user', ['users' => $users]);
            case 0:
                return $this->renderPartial('_add_user', ['users' => $users]);
            default:
                return $this->renderPartial('_select_user', ['users' => $users]);
        }
    }


    /**
     * MASQUERADE
     * ==========
     * This is called when the rep confirms the user to masquerade as, records
     * the details in the session, then redirects to the shop itself.
     *
     * @param $id
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionMasquerade($id)
    {
        if (!Yii::$app->user->can('buy_for_customer')) {
            throw new \yii\web\NotFoundHttpException;
        }

        $user = GAUser::findById($id);

        if ($user) {
            Yii::$app->session->set('masquerade_user', $user->id);
            Yii::$app->session->set('current_account', $user->account->customer_exertis_account_number);
            Yii::$app->user->setIdentity($user);

            return $this->redirect(Yii::$app->request->hostInfo . '/shop');
            exit;
        }
        throw new \yii\web\NotFoundHttpException;
    }

    /**
     * GET PENDING PROVIDER
     * ====================
     *
     * @return ActiveDataProvider
     */
    private function getPendingProvider()
    {
        // -------------------------------------------------------------------
        // Add any user provided search criteria, then ensure the critical
        // details are overwritten to avoid the user attempting to access
        // other accounts.
        // -------------------------------------------------------------------
        $userInputs = Yii::$app->request->getQueryParams();
        if (array_key_exists('SessionOrderSearch', $userInputs)) {
            $params = $userInputs;
        } else {
            $params = [];
        }

        $searchModel  = new SessionOrderSearch();
        $dataProvider = $searchModel->search($params);

        return [$searchModel, $dataProvider];

    }

    /**
     * GET RECENT ORDERS
     * =================
     *
     * @return array
     */
    private function getRecentOrders()
    {
        $userInputs = Yii::$app->request->getQueryParams();
        if (array_key_exists('SalesRepOrderSearch', $userInputs)) {
            $params = $userInputs;
        } else {
            $params = [];
        }

        $searchModel  = new SalesRepOrderSearch();
        $dataProvider = $searchModel->search($params);

        return [$searchModel, $dataProvider];
    }

    /**
     * GET PROVIDER
     * ============
     * Added a test for the account as well to support sales reps placing
     * orders, which they may do for multiple clients in one session
     *
     * @return ActiveDataProvider
     */
    private function getProvider()
    {
        $this->getUserDetails();

        // -------------------------------------------------------------------
        // Add any user provided search criteria, then ensure the critical
        // details are overwritten to avoid the user attempting to access
        // other accounts.
        // -------------------------------------------------------------------
        $userInputs = Yii::$app->request->getQueryParams();
        if (array_key_exists('StockItemSearch', $userInputs)) {
            $params = $userInputs;
        } else {
            $params = [];
        }

        $searchModel = new SalesRepSearch();

        $dataProvider = $searchModel->search($params);

        return $dataProvider;


        $query = SessionOrder::find()
            ->where(['session_id' => session_id()])
            ->andWhere(['sales_rep_id' => $this->user->id])
            ->joinWith('product')
            ->orderBy('session_order.account_id');

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }


}
