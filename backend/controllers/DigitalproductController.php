<?php

namespace backend\controllers;

use Yii;
use common\models\DigitalProduct;
use common\models\DigitalProductSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\filters\AccessControl;
use yii\web\UploadedFile;

/**
 * DigitalproductController implements the CRUD actions for DigitalProduct model.
 */
class DigitalproductController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],

            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['index', 'view', 'create','update', 'delete'],
                'rules' => [
                    [
                        'allow'   => Yii::$app->user->can("admin"),
                        'actions' => ['index', 'view', 'create', 'update', 'leaflet', 'leafletcoords', 'delete', 'ztorm-catalogue', 'ajax-add-product-to-edsr', 'ajax-disable-product', 'ajax-enable-product', 'ajax-get-product-codes'],
                        'roles'   => ['@'],
                    ],
                ],
            ],

        ];
    }

    /**
     * Lists all DigitalProduct models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new DigitalProductSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /**
     * Displays a single DigitalProduct model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }

    /**
     * Creates a new DigitalProduct model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DigitalProduct;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing DigitalProduct model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
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
     * Deletes an existing DigitalProduct model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * LEAFLET
     * =======
     * The leaflet is used to hand the product licence keys to end customers.
     * This is done by uploading an image template of the leaflet, containing
     * a space for the printed key string. The exact position to print the
     * keys is flagged using an 'editor' attached to the product detail page.
     *
     * When the image is uploaded, it is created as a file here at the back
     * end, allowing it to be referenced when the page is reloaded. However,
     * because the front end may run on a different server, the image data is
     * also stored directly in the database record.
     *
     * For large images, this storage process is demanding on the MySql server
     * and it may be necessary to alter the value of max_allowed_packet in
     * the my.cnf file, eg
     *      [mysqld]
     *          max_allowed_packet = 100M
     *
     * @param $account
     *
     * @return mixed
     * @throws \yii\web\HttpException
     *
     */
    public function actionLeaflet($id)
    {
        $result = true;

        $model   = $this->findModel($id);
        $leaflet = $model->productLeafletInfo;

        $image = UploadedFile::getInstance($leaflet, 'image');

        if ($image) {
            // -------------------------------------------------------------------
            // generate a unique file name, keeping the extension and record it
            // in the account object
            // -------------------------------------------------------------------
            $ext         = strrchr($image->name, '.');
            $leafletName = Yii::$app->security->generateRandomString() . $ext;

            // -------------------------------------------------------------------
            // now save the file in the designated directory and also the data
            // in the database. Include the mime type to ease selecting the correct
            // imageCreate<...> method at the front end
            // -------------------------------------------------------------------
            $directory = $leaflet->getBaseLeafletDirectory();

            //$mimeType = mime_content_type( $image->tempName ) ;

            $mimeType = 'image/jpeg';

//            $leaflet->image_data = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($image->tempName)) ;

            $leaflet->image_type = $mimeType;
            $leaflet->image_data = file_get_contents($image->tempName);

            if ($result = $image->saveAs($directory . $leafletName)) {
                $leaflet->deleteLeafletImage();
                $leaflet->saveNewLeafletImage($leafletName);

            } else {
                print_r($image->error);
            }
        }

        return $result;
    }

    /**
     * LEAFLET COORDS
     * ==============
     *
     * @return bool
     * @throws NotFoundHttpException
     */
    public function actionLeafletcoords($id)
    {
        $result = false;
        $model  = $this->findModel($id);
        if ($model) {
            $leaflet = $model->productLeafletInfo;
            if ($leaflet) {
                $keyPosition  = Yii::$app->request->post('key');
                $logoPosition = Yii::$app->request->post('logo');
                $namePosition = Yii::$app->request->post('name');

                $leaflet->key_xcoord     = $keyPosition['xCoord'];
                $leaflet->key_box_width  = $keyPosition['width'];
                $leaflet->key_ycoord     = $keyPosition['yCoord'];
                $leaflet->key_box_height = $keyPosition['height'];

                $leaflet->logo_xcoord     = $logoPosition['xCoord'];
                $leaflet->logo_box_width  = $logoPosition['width'];
                $leaflet->logo_ycoord     = $logoPosition['yCoord'];
                $leaflet->logo_box_height = $logoPosition['height'];

                $leaflet->name_xcoord     = $namePosition['xCoord'];
                $leaflet->name_box_width  = $namePosition['width'];
                $leaflet->name_ycoord     = $namePosition['yCoord'];
                $leaflet->name_box_height = $namePosition['height'];

                $result                  = $leaflet->save();
            }
        }

        return $result;
    }
    
    
    public function actionZtormCatalogue(){
        ini_set('max_execution_time', 120);
        $store = \common\models\ZtormAccess::findOne(['type'=>'LIVE', 'storealias'=>'EDSR']);
        
        $itemPurchaser = new \console\components\ItemPurchaser();
        $itemPurchaser->setStoreDetails($store);
        
        $displayPriceDropdown = DigitalProduct::getDisplayPriceAsOptions();
        
        $searchModel = new \common\models\ZtormCatalogueCacheSearch();        
        \Yii::beginProfile('getZtormRawCatalogue');
        $products = $searchModel->search(Yii::$app->request->queryParams);
        \Yii::endProfile('getZtormRawCatalogue');
        
        return $this->render('ztormcatalogue', ['products' => $products, 'displayPriceDropdown' => $displayPriceDropdown, 'searchModel' => $searchModel]);
        
    }
    
    
    public function actionAjaxAddProductToEdsr(){
                                
        $productId = Yii::$app->request->post('productid');
        $productName = substr(Yii::$app->request->post('productname'), 0, 255);
        $partCode = Yii::$app->request->post('partcode');
        $displayPrice = Yii::$app->request->post('displayprice');
        $fixedPrice = Yii::$app->request->post('fixedprice');    
        
        if(empty($fixedPrice)){
            $fixedPrice = 0.00;
        }
        
        //Insert product to Product Code Lookup table
        $productcodeLookup = new \common\models\ProductcodeLookup();
        $productcodeLookup->product_id = $productId;
        $productcodeLookup->name = $productName;
        $productcodeLookup->storealias = 'EDSR';
        $productcodeLookup->saveWithAuditTrail('Product #'.$productId.' has been added to Product Code Lookup Table.');
        
        
        //Get item's productcode lookup id
        $lastId = $productcodeLookup->findOne(['product_id'=>$productId]);
        
        //Insert product into Digital Product table
        $digitalproduct = new DigitalProduct();
        $digitalproduct->partcode = $partCode;
        $digitalproduct->description = $productName;
        $digitalproduct->enabled = 1;
        $digitalproduct->eztorm_id = $lastId['id'];
        $digitalproduct->display_price_as = $displayPrice;
        $digitalproduct->fixed_price = $fixedPrice;
        $digitalproduct->saveWithAuditTrail('Product #'.$productId.' has been added to Digital Product Table.');
        
            echo 'ok';
    }
    
    
    public function actionAjaxDisableProduct(){
        $productId = Yii::$app->request->post('productid');
        
        //Getting eztorm_id
        $eztormId = \common\models\ProductcodeLookup::find()->where(['product_id'=>$productId])->one();
                
        $digitalproduct = DigitalProduct::find()->where(['eztorm_id'=>$eztormId['id']])->one();
        $digitalproduct->enabled = 0;
        
        if($digitalproduct->update()){
            echo 'ok';
        } else {
            echo 'error';
        }
    }
    
    
    public function actionAjaxEnableProduct(){
        $productId = Yii::$app->request->post('productid');
        
        //Getting eztorm_id
        $eztormId = \common\models\ProductcodeLookup::find()->where(['product_id'=>$productId])->one();
                
        $digitalproduct = DigitalProduct::find()->where(['eztorm_id'=>$eztormId['id']])->one();
        $digitalproduct->enabled = 1;
        
        if($digitalproduct->update()){
            echo 'ok';
        } else {
            echo 'error';
        }
    }
    
    
    public function actionAjaxGetProductCodes(){
        $productName = Yii::$app->request->get('productname');
        
        $query = \app\models\ProductT::find()->where(['LIKE', 'short_description', $productName])->limit(5)->all();
               
        if(count($query) > 0){
            foreach($query as $partcode){
                echo '<a title="'.$partcode['short_description'].'" href="javascript:void(0)" onclick=\'addToPartCode("'.$partcode['item_code'].'")\'><i>' . $partcode['item_code'] . '</i></a><br>';
            }
        } else {
            echo 'No part code found.';
        }
        
    }
    

    /**
     * Finds the DigitalProduct model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return DigitalProduct the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DigitalProduct::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
