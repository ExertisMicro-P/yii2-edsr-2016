<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "stock_activity".
 *
 * @property integer $id
 * @property integer $stockroom_id
 * @property integer $stock_item
 * @property string $status
 * @property string $notes
 * @property string $destination_table
 * @property integer $destination_id
 * @property string $sent_to
 * @property string $sent_to_email
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property Stockroom $stockroom
 * @property User $user
 */
class StockActivity extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stock_activity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['notes'], 'required'],
            [['stockroom_id', 'stock_item', 'destination_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['status', 'notes', 'destination_table', 'sent_to', 'sent_to_email'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stockroom_id' => 'Stockroom ID',
            'stock_item' => 'Stock Item',
            'status' => 'Status',
            'notes' => 'Notes',
            'destination_table' => 'Destination Table',
            'destination_id' => 'Destination ID',
            'sent_to' => 'Sent To',
            'sent_to_email' => 'Sent To Email',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockroom()
    {
        return $this->hasOne(Stockroom::className(), ['id' => 'stockroom_id']);
    }

    /**
     * LOG
     * ===
     * Adds a log message to the table
     *
     * @param      $message
     * @param      $stockroomId
     * @param      $stockItemId
     * @param      $status
     * @param null $destinationTable
     * @param null $destinationTableId
     * @param null $sentTo
     * @param null $sentToEmail
     *
     * @throws
     */
    static public function log($message, $stockroomId=null, $stockItemId=null, $status=null, $destinationTable=null, $destinationTableId= null, $sentTo=null, $sentToEmail=null) {

        if (empty($stockroomId) && empty($stockItemId)) {
            throw new \Exception ('You must provide at least one of the stockroom or stock item ids') ;
        }

        $sact = new StockActivity ;
        $sact->notes = $message ;
        $sact->stockroom_id = $stockroomId ;
        $sact->stock_item   = $stockItemId ;
        $sact->status       = $status ;
        $sact->destination_table = $destinationTable ;
        $sact->destination_id    = $destinationTableId ;
        $sact->sent_to      = $sentTo ;
        $sact->sent_to_email = $sentToEmail ;

        $sact->save() ;
    }

}
