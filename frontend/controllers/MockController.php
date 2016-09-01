<?php
namespace frontend\controllers;

use common\models\DigitalProduct;
use common\models\Orderdetails;
use common\models\StockActivity;
use common\models\StockItem;
use Yii;
use yii\web\Controller;

class MockController extends Controller
{
    public $layout = '@frontend/views/layouts/mainnw';

    /**
     *
     */
    public function actionTest()
    {

        // -------------------------------------------------------------------
        // Grab some dummy data. For some reason the orderdetails won't
        // load, but ignore for now and create a mock
        // -------------------------------------------------------------------
        $sitem      = StockItem::findOne(92); //->one() ;
        $odet       = Orderdetails::findOne(78); //->one();
        $odet       = Orderdetails::find(78)->where(['id' => 78]); //->one();
        $product    = DigitalProduct::findOne(1);
        $odet       = new \StdClass;
        $odet->sop  = 'sop';
        $odet->name = 'Fred';

        $data = [
            [
//                'po',
//                'one',
//                [
//                    'item'         => $sitem,
//                    'orderdetails' => $odet
//                ],
                [
                    'item'         => $sitem,
                    'orderdetails' => $odet,
                    'product'      => $product
                ],
                [
                    'item'         => $sitem,
                    'orderdetails' => $odet,
                    'product'      => $product
                ],
                [
                    'item'         => $sitem,
                    'orderdetails' => $odet,
                    'product'      => $product
                ]
            ]
        ];

        // -------------------------------------------------------------------
        // Create the spreadsheet
        // -------------------------------------------------------------------
        $exc             = new \frontend\components\ExcelExporter;
        $spreadSheetName = $exc->newSalesEmail($data);

        // -------------------------------------------------------------------
        // Test adding it to an email
        // -------------------------------------------------------------------
        $mailer           = Yii::$app->mailer;
        $oldViewPath      = $mailer->viewPath;
        $mailer->viewPath = Yii::$app->params['account.emailPath'];

        $subject = 'Exertis Digital Stock Room: INTERNAL NOTIFICATION';
        $message = $mailer->compose()
            ->setTo('noel@crewe-it.co.uk')
            ->setSubject($subject)
            ->attach($spreadSheetName);

        $result = $message->send();

    }
}
