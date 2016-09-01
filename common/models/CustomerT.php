<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer_t".
 *
 * @property integer $id
 * @property string $account_number
 * @property string $account_type
 * @property string $company_name
 * @property string $inv_addr1
 * @property string $inv_addr2
 * @property string $inv_addr3
 * @property string $inv_addr4
 * @property string $inv_postcode
 * @property string $inv_county
 * @property string $inv_country
 * @property string $shipto_addr1
 * @property string $shipto_addr2
 * @property string $shipto_addr3
 * @property string $shipto_addr4
 * @property string $shipto_postcode
 * @property string $shipto_county
 * @property string $shipto_country
 * @property string $vat_code
 * @property string $fixed_shipping
 * @property string $shipping_charge
 * @property string $payment_terms
 * @property string $telephone_number
 * @property string $shipping_type
 * @property string $active_flag
 * @property string $created
 * @property string $last_updated
 */
class CustomerT extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_t';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('creditDb');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_number'], 'required'],
            [['created', 'last_updated'], 'safe'],
            [['account_number'], 'string', 'max' => 10],
            [['account_type', 'inv_postcode', 'shipto_postcode', 'vat_code', 'fixed_shipping', 'shipping_charge', 'payment_terms'], 'string', 'max' => 20],
            [['company_name', 'inv_addr1', 'inv_addr2', 'inv_addr3', 'inv_addr4', 'inv_county', 'inv_country', 'shipto_addr1', 'shipto_addr2', 'shipto_addr3', 'shipto_addr4', 'shipto_county', 'shipto_country'], 'string', 'max' => 500],
            [['telephone_number', 'shipping_type'], 'string', 'max' => 50],
            [['active_flag'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_number' => 'Account Number',
            'account_type' => 'Account Type',
            'company_name' => 'Company Name',
            'inv_addr1' => 'Inv Addr1',
            'inv_addr2' => 'Inv Addr2',
            'inv_addr3' => 'Inv Addr3',
            'inv_addr4' => 'Inv Addr4',
            'inv_postcode' => 'Inv Postcode',
            'inv_county' => 'Inv County',
            'inv_country' => 'Inv Country',
            'shipto_addr1' => 'Shipto Addr1',
            'shipto_addr2' => 'Shipto Addr2',
            'shipto_addr3' => 'Shipto Addr3',
            'shipto_addr4' => 'Shipto Addr4',
            'shipto_postcode' => 'Shipto Postcode',
            'shipto_county' => 'Shipto County',
            'shipto_country' => 'Shipto Country',
            'vat_code' => 'Vat Code',
            'fixed_shipping' => 'Fixed Shipping',
            'shipping_charge' => 'Shipping Charge',
            'payment_terms' => 'Payment Terms',
            'telephone_number' => 'Telephone Number',
            'shipping_type' => 'Shipping Type',
            'active_flag' => 'Active Flag',
            'created' => 'Created',
            'last_updated' => 'Last Updated',
        ];
    }
}
