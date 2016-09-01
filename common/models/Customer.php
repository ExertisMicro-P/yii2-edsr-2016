<?php

namespace common\models;
use exertis\savewithaudittrail\SaveWithAuditTrailBehavior;
use Yii;
use common\models\CustomerQuery;

/**
 * This is the model class for table "customer".
 *
 * @property string $exertis_account_number
 * @property string $status
 * @property string $name
 * @property string $invoicing_address_line1
 * @property string $invoicing_address_line2
 * @property string $invoicing_address_line3
 * @property string $invoicing_address_line4
 * @property string $invoicing_postcode
 * @property string $invoicing_city
 * @property string $invoicing_country_code
 * @property string $delivery_address_line1
 * @property string $delivery_address_line2
 * @property string $delivery_address_line3
 * @property string $delivery_address_line4
 * @property string $delivery_postcode
 * @property string $delivery_city
 * @property string $delivery_country_code
 * @property string $vat_code
 * @property string $fixed_shipping_flag
 * @property string $fixed_shipping_charge
 * @property string $payment_terms
 * @property string $phone_number
 * @property string $shipping_method
 * @property string $unknown1
 * @property string $unknown2
 * @property string $timestamp
 */
class Customer extends \yii\db\ActiveRecord
{
    const STATUS_TRANSACTIONAL = 'T';
    const STATUS_HOLD = '2';
    const STATUS_STOP = '3';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer';
    }

    /**
     * @inheritdoc
     * @return AccountQuery
     */
    public static function find()
    {
        return new CustomerQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['exertis_account_number'], 'required'],
            [['name'], 'string', 'max'=>255],
            [['timestamp'], 'safe'],
            [['exertis_account_number', 'vat_code', 'fixed_shipping_charge'], 'string', 'max' => 20],
            [['status', 'fixed_shipping_flag', 'payment_terms'], 'string', 'max' => 1],
            [['invoicing_address_line1', 'invoicing_address_line2', 'invoicing_address_line3', 'invoicing_address_line4', 'delivery_address_line1', 'delivery_address_line2', 'delivery_address_line3', 'delivery_address_line4'], 'string', 'max' => 240],
            [['invoicing_postcode', 'invoicing_city', 'delivery_postcode', 'delivery_city'], 'string', 'max' => 60],
            [['invoicing_country_code', 'delivery_country_code'], 'string', 'max' => 10],
            [['phone_number', 'shipping_method', 'unknown1', 'unknown2'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'exertis_account_number' => Yii::t('app', 'Exertis Account Number'),
            'status' => Yii::t('app', 'Status'),
            'name' => Yii::t('app', 'Name'),
            'invoicing_address_line1' => Yii::t('app', 'Invoicing Address Line1'),
            'invoicing_address_line2' => Yii::t('app', 'Invoicing Address Line2'),
            'invoicing_address_line3' => Yii::t('app', 'Invoicing Address Line3'),
            'invoicing_address_line4' => Yii::t('app', 'Invoicing Address Line4'),
            'invoicing_postcode' => Yii::t('app', 'Invoicing Postcode'),
            'invoicing_city' => Yii::t('app', 'Invoicing City'),
            'invoicing_country_code' => Yii::t('app', 'Invoicing Country Code'),
            'delivery_address_line1' => Yii::t('app', 'Delivery Address Line1'),
            'delivery_address_line2' => Yii::t('app', 'Delivery Address Line2'),
            'delivery_address_line3' => Yii::t('app', 'Delivery Address Line3'),
            'delivery_address_line4' => Yii::t('app', 'Delivery Address Line4'),
            'delivery_postcode' => Yii::t('app', 'Delivery Postcode'),
            'delivery_city' => Yii::t('app', 'Delivery City'),
            'delivery_country_code' => Yii::t('app', 'Delivery Country Code'),
            'vat_code' => Yii::t('app', 'Vat Code'),
            'fixed_shipping_flag' => Yii::t('app', 'Fixed Shipping Flag'),
            'fixed_shipping_charge' => Yii::t('app', 'Fixed Shipping Charge'),
            'payment_terms' => Yii::t('app', 'Payment Terms'),
            'phone_number' => Yii::t('app', 'Phone Number'),
            'shipping_method' => Yii::t('app', 'Shipping Method'),
            'unknown1' => Yii::t('app', 'Unknown1'),
            'unknown2' => Yii::t('app', 'Unknown2'),
            'timestamp' => Yii::t('app', 'Timestamp'),
        ];
    }
    /**
     *
     * @return type
     */
    public function behaviors() {
        return [
            [
                'class' => SaveWithAuditTrailBehavior::className(),
                'userClass' => '\common\models\gauth\GAUser',
            ],
            //Taggable::className(),

        ];
    }

    /**
     * Builds and executes a SQL statement for truncating the DB table.
     * @param string $table the table to be truncated. The name will be properly quoted by the method.
     */
    public static function truncateTable()
    {
        \Yii::$app->getDb()->createCommand('SET foreign_key_checks = 0;')->execute();
        \Yii::$app->getDb()->createCommand()->truncateTable(self::tableName())->execute();
        Yii::trace('Dropped table before file processing' .__METHOD__.':'.__LINE__);
        \Yii::$app->getDb()->createCommand('SET foreign_key_checks = 1;')->execute();
    }


    public function getAccount()
    {
        // Customer is related to to a account record
        return $this->hasOne(Account::className(), ['customer_exertis_account_number' => 'exertis_account_number'])->inverseOf('customer');;
    }
}
