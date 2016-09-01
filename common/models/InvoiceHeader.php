<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "invoice_header".
 *
 * @property integer $ih_header_row_id
 * @property string $ih_invoice_number
 * @property string $ih_invoice_type
 * @property string $ih_account_number
 * @property string $ih_001
 * @property string $ih_002
 * @property string $ih_003
 * @property string $ih_004
 * @property string $ih_005
 * @property string $ih_006
 * @property string $ih_007
 * @property string $ih_008
 * @property string $ih_009
 * @property string $ih_010
 * @property string $ih_011
 * @property string $ih_012
 * @property string $ih_013
 * @property string $ih_014
 * @property string $ih_015
 * @property string $ih_016
 * @property string $ih_017
 * @property string $ih_018
 * @property string $ih_019
 * @property string $ih_020
 * @property string $ih_021
 * @property string $ih_022
 * @property string $ih_023
 * @property string $ih_024
 * @property string $ih_025
 * @property string $ih_026
 * @property string $ih_027
 * @property string $ih_028
 * @property string $ih_029
 * @property string $ih_030
 * @property string $ih_031
 * @property string $ih_032
 * @property string $ih_033
 * @property string $ih_034
 * @property string $ih_import_date
 * @property string $ih_import_line
 * @property string $ih_invoice_status
 * @property integer $ih_invoice_type_int
 * @property integer $ih_invoice_status_int
 * @property string $active_flag
 * @property string $created
 * @property string $last_updated
 */
class InvoiceHeader extends \yii\db\ActiveRecord
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
        return '[MMAV2].[dbo].[tbl_invoice_header]';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ih_invoice_number'], 'required'],
            [['ih_import_date', 'created', 'last_updated'], 'safe'],
            [['ih_invoice_type_int', 'ih_invoice_status_int'], 'integer'],
            [['ih_invoice_number', 'ih_invoice_type', 'ih_account_number', 'ih_001', 'ih_002', 'ih_003', 'ih_004', 'ih_005', 'ih_006', 'ih_007', 'ih_008', 'ih_009', 'ih_010', 'ih_011', 'ih_012', 'ih_013', 'ih_014', 'ih_015', 'ih_016', 'ih_017', 'ih_018', 'ih_019', 'ih_020', 'ih_021', 'ih_022', 'ih_023', 'ih_024', 'ih_025', 'ih_026', 'ih_027', 'ih_028', 'ih_029', 'ih_030', 'ih_031', 'ih_032', 'ih_033', 'ih_034', 'ih_import_line', 'ih_invoice_status', 'active_flag'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ih_header_row_id' => 'Header Row ID',
            'ih_invoice_number' => 'Invoice Number',
            'ih_invoice_type' => 'Invoice Type',
            'ih_account_number' => 'Account Number',
            'ih_001' => 'Ih 001',
            'ih_002' => 'Ih 002',
            'ih_003' => 'Ih 003',
            'ih_004' => 'Ih 004',
            'ih_005' => 'Ih 005',
            'ih_006' => 'Ih 006',
            'ih_007' => 'Ih 007',
            'ih_008' => 'Ih 008',
            'ih_009' => 'Ih 009',
            'ih_010' => 'Ih 010',
            'ih_011' => 'Ih 011',
            'ih_012' => 'Ih 012',
            'ih_013' => 'Ih 013',
            'ih_014' => 'Ih 014',
            'ih_015' => 'Ih 015',
            'ih_016' => 'Ih 016',
            'ih_017' => 'Ih 017',
            'ih_018' => 'Ih 018',
            'ih_019' => 'Ih 019',
            'ih_020' => 'Ih 020',
            'ih_021' => 'Ih 021',
            'ih_022' => 'Ih 022',
            'ih_023' => 'PO',
            'ih_024' => 'Ih 024',
            'ih_025' => 'Ih 025',
            'ih_026' => 'Ih 026',
            'ih_027' => 'Ih 027',
            'ih_028' => 'Ih 028',
            'ih_029' => 'Ih 029',
            'ih_030' => 'Ih 030',
            'ih_031' => 'Ih 031',
            'ih_032' => 'Ih 032',
            'ih_033' => 'Ih 033',
            'ih_034' => 'Ih 034',
            'ih_import_date' => 'Ih Import Date',
            'ih_import_line' => 'Ih Import Line',
            'ih_invoice_status' => 'Ih Invoice Status',
            'ih_invoice_type_int' => 'Ih Invoice Type Int',
            'ih_invoice_status_int' => 'Ih Invoice Status Int',
            'active_flag' => 'Active Flag',
            'created' => 'Created',
            'last_updated' => 'Last Updated',
        ];
    }
    
    
    
    public function getInvoiceTrailer(){
        return $this->hasOne(invoiceTrailer::className(), ['it_invoice_number'=>'ih_invoice_number']);
    }

}
