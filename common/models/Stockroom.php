<?php


namespace common\models;

use Yii;
use console\components\FileFeedErrorCodes;
use console\components\AccountSetupException;
use common\models\Account;
use exertis\savewithaudittrail\SaveWithAuditTrailBehavior;

/**
 * This is the model class for table "stockroom".
 *
 * @property integer     $id
 * @property integer     $account_id
 * @property string      $name
 *
 * @property StockItem[] $stockItems
 * @property Account     $account
 */
class Stockroom extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stockroom';
    }


    /**
     * @inheritdoc
     */
    /*   public function behaviors()
       {
           return [
               //TimestampBehavior::className(),

               'LoggableBehavior'=> [
                      'class' => LoggableBehavior::className(),
                  ]
          ];

       }*/

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id'], 'required'],
            [['account_id'], 'integer'],
            [['name'], 'string', 'max' => 128]
        ];
    }

    /**
     *
     * @return type
     */
    public function behaviors()
    {
        return [
            [
                'class'     => SaveWithAuditTrailBehavior::className(),
                'userClass' => '\common\models\gauth\GAUser',
            ],
            //Taggable::className(),

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'account_id' => 'Account ID',
            'name'       => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockItems()
    {
        // RCH 20160802
        // hide any which are spare
        return $this->hasMany(StockItem::className(), ['stockroom_id' => 'id'])->where(['spare'=>0]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }

    public function createNewStockRoom($account_id, $label = 'main stock room')
    {
        $this->name       = $label;
        $this->account_id = $account_id;
        if (!$this->saveWithAuditTrail('Created for account id ' . $account_id)) {
            Yii::error(__METHOD__ . 'Stockroom could not be created for ' . $account_id . ' ' . print_r($this->getErrors(), true));
            throw new AccountSetupException(FileFeedErrorCodes::STOCKROOM_SAVE_FAILED,
                print_r($this->getErrors(), true));
        }
    }

    public function getStockItemsCount()
    {
        return StockItem::find()->where(['stockroom_id' => $this->id])->count();
    }

    public function getStockItemTotal($itemId)
    {
        return $this->hasMany(StockItem::className(), ['stockroom_id' => 'id'])
            ->where('digital_product_id', '=', $itemId);
    }


}
