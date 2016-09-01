<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "product_t".
 *
 * @property integer $id
 * @property string $item_code
 * @property string $short_description
 * @property string $long_description
 * @property double $srp
 * @property double $weight
 * @property double $cost_price
 * @property integer $qty_available
 * @property integer $manu_id
 * @property integer $category
 * @property double $vat_rate
 * @property string $manu_part_number
 * @property string $manu_part_number_sanitised
 * @property string $range
 * @property string $env_tax_ammount
 * @property string $grp_desc
 * @property double $sales_price_1
 * @property double $sales_price_2
 * @property double $sales_price_3
 * @property string $product_status
 * @property string $cat1
 * @property string $cat2
 * @property string $cat3
 * @property string $cat4
 * @property string $cat5
 * @property string $cat6
 * @property string $cat7
 * @property string $cat8
 * @property string $topcat
 * @property string $quickcat
 * @property string $webcat1
 * @property string $webcat2
 * @property string $webcat3
 * @property string $webcat4
 * @property string $item_status
 * @property string $spec_url
 * @property string $manu
 * @property string $stock_group
 * @property string $sdp
 * @property string $division
 * @property string $active_flag
 * @property string $created
 * @property string $last_updated
 */
class SharedDataPoolProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_t';
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
            [['item_code'], 'required'],
            [['long_description'], 'string'],
            [['srp', 'weight', 'cost_price', 'vat_rate', 'sales_price_1', 'sales_price_2', 'sales_price_3'], 'number'],
            [['qty_available', 'manu_id', 'category'], 'integer'],
            [['created', 'last_updated'], 'safe'],
            [['item_code', 'manu_part_number', 'manu_part_number_sanitised', 'range', 'env_tax_ammount', 'grp_desc', 'cat1', 'cat2', 'cat3', 'cat4', 'cat5', 'cat6', 'cat7', 'cat8', 'topcat', 'quickcat', 'webcat1', 'webcat2', 'webcat3', 'webcat4', 'spec_url', 'manu', 'stock_group', 'sdp', 'division'], 'string', 'max' => 500],
            [['short_description'], 'string', 'max' => 1000],
            [['product_status', 'item_status'], 'string', 'max' => 50],
            [['active_flag'], 'string', 'max' => 5]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_code' => 'Item Code',
            'short_description' => 'Short Description',
            'long_description' => 'Long Description',
            'srp' => 'Srp',
            'weight' => 'Weight',
            'cost_price' => 'Cost Price',
            'qty_available' => 'Qty Available',
            'manu_id' => 'Manu ID',
            'category' => 'Category',
            'vat_rate' => 'Vat Rate',
            'manu_part_number' => 'Manu Part Number',
            'manu_part_number_sanitised' => 'Manu Part Number Sanitised',
            'range' => 'Range',
            'env_tax_ammount' => 'Env Tax Ammount',
            'grp_desc' => 'Grp Desc',
            'sales_price_1' => 'Sales Price 1',
            'sales_price_2' => 'Sales Price 2',
            'sales_price_3' => 'Sales Price 3',
            'product_status' => 'Product Status',
            'cat1' => 'Cat1',
            'cat2' => 'Cat2',
            'cat3' => 'Cat3',
            'cat4' => 'Cat4',
            'cat5' => 'Cat5',
            'cat6' => 'Cat6',
            'cat7' => 'Cat7',
            'cat8' => 'Cat8',
            'topcat' => 'Topcat',
            'quickcat' => 'Quickcat',
            'webcat1' => 'Webcat1',
            'webcat2' => 'Webcat2',
            'webcat3' => 'Webcat3',
            'webcat4' => 'Webcat4',
            'item_status' => 'Item Status',
            'spec_url' => 'Spec Url',
            'manu' => 'Manu',
            'stock_group' => 'Stock Group',
            'sdp' => 'Sdp',
            'division' => 'Division',
            'active_flag' => 'Active Flag',
            'created' => 'Created',
            'last_updated' => 'Last Updated',
        ];
    }
}
