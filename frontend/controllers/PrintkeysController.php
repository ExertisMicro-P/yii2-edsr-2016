<?php
namespace frontend\controllers;

use Yii;
use Url;
use yii\web\BadRequestHttpException;
use yii\filters\AccessControl;
use common\models\StockActivity;
use frontend\controllers\yiicomp\StockroomController;

use common\models\EmailedItem;

use common\components\EmailKeys;
use common\components\PrintKeys;

//use Knp\Snappy\Pdf;

/**
 * Site controller
 */
class PrintkeysController extends yiicomp\StockroomController {


    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'reprint'],
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
     * This is used to skip CSRF checks on the post for the print request
     *
     * @param \yii\base\Action $action
     *
     * @return bool
     * @throws BadRequestHttpException
     */

    public function beforeAction($action) {
        if ($action->id == 'index') {
            Yii::$app->controller->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }


    /**
     * INDEX
     * =====
     *
     * @return \yii\web\Response
     */
    public function actionIndex() {
        $keys = explode(',', Yii::$app->request->post('pdfkeys'));

        return $this->printKeys($keys, true);
    }

    /**
     * ACTION REPRINT
     * ==============
     *
     * @return string|void|\yii\web\Response
     */
    public function actionReprint() {
        $keys = explode(',', Yii::$app->request->get('pdfkeys'));

        return $this->printKeys($keys, false);
    }


    /**
     * PRINT KEYS
     * ==========
     *
     * @param $stockitem_ids
     * @param $unpurchasedOnly
     *
     * @return string|void|\yii\web\Response
     * @throws
     * @throws \Exception
     */
    private function printKeys($stockitem_ids, $unpurchasedOnly) {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect('/user/index');
        }

        if (($user = $this->getUserDetails()) === false) {
            return $this->redirect('/user/index');

        } else {
            $this->layout = false;

            $stockitem_ids = $this->validateStockKeys($user, $stockitem_ids, $unpurchasedOnly);

            StockActivity::log('Printing keys for ' . count($stockitem_ids) . ' products', $this->stockroomId,
                               null, null,
                               EmailedItem::tableName(), null
            );

            // ---------------------------------------------------------------
            // If we're re-printing, we don't need to record the details
            // ---------------------------------------------------------------
            if ($unpurchasedOnly) {
                $result = $this->flagPrintingKeys($stockitem_ids);
            } else {
                $result = true;
            }

            if ($result === true) {
                $keyPrinter = new printKeys($this->user->account, $this->viewPath);

                return $keyPrinter->printKeys($stockitem_ids);

            } else {
                $status = 404;
                print_r($result);

                return 'Invalid request';
            }
        }
    }

    /**
     * FLAG PRINTING KEYS
     * ==================
     * Updates the database to indicate that these keys have been delivered,
     * though it doesn't actually flag that this was via prinring
     *
     * @param $stockitem_ids
     *
     * @return bool
     */
    private function flagPrintingKeys($stockitem_ids) {
        $recipientDetails = [
            'email'       => null,
            'recipient'   => null,
            'orderNumber' => 'PrintedKeys' . date('d-m-Y H:i:s'),
            'message'     => '',
            'errors'      => [],
        ];

        $emailer = new EmailKeys();
        $account = \common\models\Account::find()->where(['id' => $this->user->account_id])->one();

        return $emailer->markKeysDelivered($recipientDetails, $stockitem_ids, $account);
    }

}

