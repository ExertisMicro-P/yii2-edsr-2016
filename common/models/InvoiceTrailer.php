<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "invoice_trailer".
 *
 * @property integer $it_trailer_row_id
 * @property string $it_invoice_number
 * @property string $it_001
 * @property string $it_002
 * @property string $it_003
 * @property string $it_004
 * @property string $it_005
 * @property string $it_006
 * @property string $it_007
 * @property string $it_008
 * @property string $it_009
 * @property string $it_010
 * @property string $it_011
 * @property string $it_012
 * @property string $it_013
 * @property string $it_014
 * @property string $it_import_date
 * @property string $it_import_line
 * @property string $active_flag
 * @property string $created
 * @property string $last_updated
 */
class InvoiceTrailer extends \yii\db\ActiveRecord
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
        //return 'invoice_trailer';
        return '[MMAV2].[dbo].[tbl_invoice_trailer]';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['it_003'], 'string'],
            [['it_import_date', 'created', 'last_updated'], 'safe'],
            [['it_invoice_number', 'it_001', 'it_002', 'it_004', 'it_005', 'it_006', 'it_007', 'it_008', 'it_009', 'it_010', 'it_011', 'it_012', 'it_013', 'it_014', 'it_import_line', 'active_flag'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'it_trailer_row_id' => 'It Trailer Row ID',
            'it_invoice_number' => 'It Invoice Number',
            'it_001' => 'It 001',
            'it_002' => 'It 002',
            'it_003' => 'It 003',
            'it_004' => 'It 004',
            'it_005' => 'It 005',
            'it_006' => 'It 006',
            'it_007' => 'It 007',
            'it_008' => 'It 008',
            'it_009' => 'It 009',
            'it_010' => 'It 010',
            'it_011' => 'It 011',
            'it_012' => 'Inovice Total',
            'it_013' => 'It 013',
            'it_014' => 'It 014',
            'it_import_date' => 'It Import Date',
            'it_import_line' => 'It Import Line',
            'active_flag' => 'Active Flag',
            'created' => 'Created',
            'last_updated' => 'Last Updated',
        ];
    }
    
}
