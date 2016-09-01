<?php

namespace common\components;

use Yii;
use yii\console\Exception;
use common\models\SharedDataPoolPricat;
use common\models\SharedDataPoolProduct;

/**
 * Class CustomerPrice
 *
 * @package common\components
 */
class CustomerPrice {

    const EXCEPTION_CODE_PARAMS_MISSING = 8000;
    const EXCEPTION_CODE_PARTCODE_UNKNOWN = 8001;

    static public function getPrice($account_number, $partcode) {
        $partcode = trim($partcode);
        
        if (!empty($account_number) && !empty($partcode)) {
            // First look for Customer specific Pricing
            $customer_pricing = SharedDataPoolPricat::findOne([
                        'account_number' => $account_number,
                        'item_code' => $partcode
            ]);
            

            if (empty($customer_pricing)) {
                // no customer specific price. Fallback to standard pricing
                $standard_pricing = SharedDataPoolProduct::find()
                            ->select('sales_price_1')
                            ->where(['item_code' => $partcode])
                            ->one();

                if (empty($standard_pricing)) {
                    // no standard price. ERROR!
                    $msg = __METHOD__.': Can\'t get Price - Partcode ('.$partcode.') not known';
                    \Yii::error($msg);
                    throw new Exception($msg, self::EXCEPTION_CODE_PARTCODE_UNKNOWN);
                } else {
                    // Customer pricing was found
                    return number_format($standard_pricing->sales_price_1, 2, '.', '');
                }
            } else {
                // Customer pricing was found
                return number_format($customer_pricing->sell_price, 2, '.', '');
            }
        } else {
            throw new Exception('Can\'t get Price - parameter(s) missing', self::EXCEPTION_CODE_PARAMS_MISSING);
        }
    } // getPrice

}
