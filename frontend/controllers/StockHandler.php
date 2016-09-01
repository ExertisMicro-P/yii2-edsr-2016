<?php
/**
 * Created by PhpStorm.
 * User: noelwalsh
 * Date: 08/12/2014
 * Time: 16:38
 */

namespace frontend\controllers ;

use common\models\DigitalProduct;
use common\models\ProductImage;
use common\models\StockItem;
use Yii;
use yii\web\Controller;
use common\models\Stockroom;
use common\models\StockItemSearch;
use yii\filters\AccessControl;

use common\models\Accounts;

class StockHandler {
    private $controller ;

    public function __construct(Controller $controller) {
        $this->controller = $controller ;
    }

    private function saveTestImage() {
        $prod = DigitalProduct::findOne(1) ;
    //        print_r($prod) ;exit;
        $image = new ProductImage() ;
        $image->digital_product_id = $prod->id ;

        $image->image_url = 'http://www.solotimberframe.co.uk/uploads/page/3/thumbs/location_inline.png' ;
        $image->image_tn  = 'http://www.solotimberframe.co.uk/uploads/page/3/thumbs/location_inline.png' ;
        if ($image->save () ) {
            $prod->link('images', $image) ;
            die('ok') ;
        }
        echo '<pre>' ;
        print_r($image->errors) ;
        die('failed') ;

    }

    /**
     * GET ORDER TABLE
     * ===============
     * @param $stockroomId
     *
     * @return array|string
     */
    public function getOrdertable ($stockroomId, $grouped=true) {

        // -------------------------------------------------------------------
        // Add any user provided search criteria, then ensure the critical
        // details are overwritten to avoid the user attempting to access
        // other accounts.
        // -------------------------------------------------------------------
        $userInputs = Yii::$app->request->getQueryParams() ;
        if (array_key_exists('StockItemSearch', $userInputs)) {
            $params = $userInputs ;
        } else {
            $params = [] ;
        }

        $params['StockItemSearch']['stockroom_id'] = $stockroomId ;
//        $params['StockItemSearch']['status'] = StockItem::STATUS_PURCHASED ;


        // -------------------------------------------------------------------
        // -------------------------------------------------------------------
        $searchModel = new StockItemSearch;
        if ($grouped) {
            $dataProvider = $searchModel->searchGrouped($params);
        } else {
            $dataProvider = $searchModel->search($params);
        }

//        print_r($params);
//        print_r($userInputs) ;
//        print_r($_REQUEST) ;exit;
        return array ('orderDetails' => ['model' => $searchModel, 'provider' => $dataProvider]) ;

    }



    /**
     * GET ORDERED LIST
     * ================
     * @param $stockroomId
     *
     * @return array|string
     */
    public function getOrderedList($stockroomId) {

        // -------------------------------------------------------------------
        // Add any user provided search criteria, then ensure the critical
        // details are overwritten to avoid the user attempting to access
        // other accounts.
        // -------------------------------------------------------------------
        $userInputs = Yii::$app->request->getQueryParams() ;
        if (array_key_exists('StockItemSearch', $userInputs)) {
            $params = $userInputs ;
        } else {
            $params = [] ;
        }

        $params['StockItemSearch']['stockroom_id'] = $stockroomId ;

        // -------------------------------------------------------------------
        // -------------------------------------------------------------------
        $searchModel = new StockItemSearch;
        $dataProvider = $searchModel->searchEmailed($params);

//        print_r($params);
//        print_r($userInputs) ;
//        print_r($_REQUEST) ;exit;
        return array ('orderDetails' => ['model' => $searchModel, 'provider' => $dataProvider]) ;

    }

}
