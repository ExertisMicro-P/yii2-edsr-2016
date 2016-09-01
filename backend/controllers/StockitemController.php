<?php

namespace backend\controllers;

use Yii;
use common\models\StockItem;
use common\models\StockItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


/**
 * StockitemController implements the CRUD actions for StockItem model.
 */
class StockitemController extends Controller {

    public function behaviors() {
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'sales-index', 'send-notification'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => Yii::$app->user->can("monitor_sales"),
                        'actions' => ['sales-index'],
                        'roles' => ['@'],
                    ],
                ],
            ],

        ];
    }

    /**
     * Lists all StockItem models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new StockItemSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
    }

    /**
     * Lists all StockItem models.
     * Special View for Sales
     * @return mixed
     */
    public function actionSalesIndex() {
        $searchModel = new StockItemSearch;
        $dataProvider = $searchModel->searchSales(Yii::$app->request->getQueryParams());

        return $this->render('sales-index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single StockItem model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model = $this->findModel($id);

        $post = Yii::$app->request->post();
        /*if ($post['StockItem']['tagsForInput']) {
            $post['StockItem']['tagsNames'] = $post['StockItem']['tagsForInput'];
        }*/
        
        //Emailed items
        $emailedItemSearchModel = new \common\models\EmailedItemSearch();
        $emailedItemDataProvider = $emailedItemSearchModel->search(['id'=>$id]);

        // Gather Audit Trail
        $audittrailSearchModel = new \common\models\AudittrailSearch();
        $audittrailDataProvider = $audittrailSearchModel->search(['AudittrailSearch' => ['table_name' => StockItem::tableName(), 'record_id'=>intval($id)]]);



        if ($model->load($post) && $model->save()) {
        return $this->redirect(['view', 'id' => $model->id]);

        } else {



            return $this->render('view', [
                    'model' => $model,
                    'audittrailDataProvider' => $audittrailDataProvider,
                    'audittrailSearchModel' => $audittrailSearchModel,
                    'emailedItemDataProvider' => $emailedItemDataProvider,
                    'emailedItemSearchModel' => $emailedItemSearchModel
                    ]);
        }
    }

    /**
     * Creates a new StockItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new StockItem;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing StockItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post() && $model->save())) {
                return $this->redirect(['view', 'id' => $model->id]);

        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing StockItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    /**
     * Send an email notification for the selected stock item
     *
     * @param type $id
     * @return type
     */
    public function actionSendNotification($id) {
        $model = $this->findModel($id);

        $stockitememailer = new \console\components\OrderFeedFile\StockItemEmailer();
        $stockitememailer->notifyCustomerofNewStockItems(array($model), false);  // false means don't email Sales Team

        Yii::$app->getSession()->setFlash('success', 'Email notification sent');
        return $this->redirect(['view', 'id'=>$id]);
    }

    /**
     * Finds the StockItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StockItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = StockItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
