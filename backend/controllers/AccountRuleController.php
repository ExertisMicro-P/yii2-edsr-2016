<?php

namespace backend\controllers;

use Yii;
use backend\models\AccountRule;
use backend\models\AccountRuleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AccountRuleController implements the CRUD actions for AccountRule model.
 */
class AccountRuleController extends Controller
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
        ];
    }

    /**
     * Lists all AccountRule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AccountRuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AccountRule model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AccountRule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AccountRule();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AccountRule model.
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
     * Deletes an existing AccountRule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    
    
    public function actionTestRuleMapping($account){
        
        $rulesForAccount = \backend\models\AccountRuleMapping::findOne(['account_id'=>$account]);
        $AllRulesForAccount = explode(',', $rulesForAccount->account_rule_id);
        $sqlWhere = '';
        
        foreach($AllRulesForAccount as $rule){
            
            $ruleCondition = AccountRule::findOne(['id'=>$rule])->ruleQuery;
            
            
            $sqlWhere .= $ruleCondition . ' AND ';
            
        }
        $sqlWhere = substr($sqlWhere, 0, -5);
        
        $sqlQuery = Yii::$app->db->createCommand("SELECT * FROM ztorm_catalogue_cache WHERE ".$sqlWhere."")->queryAll();
        
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $sqlQuery,
        ]);
        
        return $this->render('test-rule-mapping', ['dataProvider' => $dataProvider]);
        
        
    }
    
    
    

    /**
     * Finds the AccountRule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AccountRule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AccountRule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
