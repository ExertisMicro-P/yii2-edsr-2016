<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "invoice_detail".
 *
 * @property integer $id_detail_row_id
 * @property string $id_invoice_number
 * @property string $id_001
 * @property string $id_002
 * @property string $id_003
 * @property string $id_004
 * @property string $id_005
 * @property string $id_006
 * @property string $id_007
 * @property string $id_008
 * @property string $id_009
 * @property string $id_010
 * @property string $id_011
 * @property string $id_012
 * @property string $id_013
 * @property string $id_014
 * @property string $id_015
 * @property string $id_016
 * @property string $id_import_date
 * @property string $id_import_line
 * @property string $active_flag
 * @property string $created
 * @property string $last_updated
 */
class InvoiceDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return Yii::$app->invoicesDb;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        //return 'invoice_detail';
        return '[MMAV2].[dbo].[tbl_invoice_detail]';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_invoice_number'], 'required'],
            [['id_import_date', 'created', 'last_updated'], 'safe'],
            [['id_invoice_number', 'id_001', 'id_002', 'id_003', 'id_004', 'id_005', 'id_006', 'id_007', 'id_008', 'id_009', 'id_010', 'id_011', 'id_012', 'id_013', 'id_014', 'id_015', 'id_016', 'id_import_line', 'active_flag'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_detail_row_id' => 'Id Detail Row ID',
            'id_invoice_number' => 'Id Invoice Number',
            'id_001' => 'Id 001',
            'id_002' => 'Id 002',
            'id_003' => 'Id 003',
            'id_004' => 'Id 004',
            'id_005' => 'Id 005',
            'id_006' => 'Id 006',
            'id_007' => 'Id 007',
            'id_008' => 'Id 008',
            'id_009' => 'Id 009',
            'id_010' => 'Id 010',
            'id_011' => 'Id 011',
            'id_012' => 'Id 012',
            'id_013' => 'Id 013',
            'id_014' => 'Id 014',
            'id_015' => 'Id 015',
            'id_016' => 'Id 016',
            'id_import_date' => 'Id Import Date',
            'id_import_line' => 'Id Import Line',
            'active_flag' => 'Active Flag',
            'created' => 'Created',
            'last_updated' => 'Last Updated',
        ];
    }
}
