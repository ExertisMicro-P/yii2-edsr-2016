<?php
namespace common\models;

class InvoiceDetailCostsViewModel extends \yii\base\Model
{   
    public $delivery;
    public $totalExVat;
    public $vatCode;
    public $vat;
    public $totalIncVat;

    public function attributeLabels()
    {
        return [
            'delivery' => 'Delivery Charge',
            'totalExVat' => 'Total ex VAT',
            'vatCode' => 'VAT Code',
            'vat' => 'VAT',
            'totalIncVat' => 'Total inc VAT',
        ];
    }
}

