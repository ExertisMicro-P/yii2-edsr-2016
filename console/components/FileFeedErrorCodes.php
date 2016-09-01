<?php


/**
 * Description of CampaignErrorCodes
 * List of const codes used for reporting errors in the Campaign app
 * @author helenk
 */
//actual enum
namespace console\components;
use common\components\BaseEnum;
final class FileFeedErrorCodes extends BaseEnum{
    //put your code here
    const CUSTOMER_FEED_SAVE_FAILED = 100;
    const CUSTOMER_NOT_ONE_KNOWN = 110;
    const PRODUCT_FEED_SAVE_FAILED = 200;
    const ORDER_FEED_SAVE_FAILED = 300;
    const ACCOUNT_SAVE_FAILED = 400;
    const USER_SAVE_FAILED = 500;
    const STOCKROOM_SAVE_FAILED = 520;
    const STOCKITEM_SAVE_FAILED = 540;
    const STOCKITEM_NOT_PURCHASED = 560;
    const ITEM_NOT_MAPPED_IN_CATALOGUE = 565;
    const ORDERDETAILS_SAVE_FAILED = 580;
    const INSTALL_KEY_NOT_RX = 585;
    const ZSTORM_USER_ALREADY_OWNS_PRODUCT = 1006; //DO NOT CHANGE THIS VALUE IT IS A eZTORM SPECIFIC
    const SOP_LOOKUP_FAILED = 420;
    const HEADER_SOP_LOOKUP_FAILED = 430;
    const SOPLOOKUP_SAVE_FAILED = 440;
    const PRODUCT_LOOKUP_SAVE_FAILED = 210;
    const CURL_FAILED = 700;
    const CURL_FAILED_NO_RESPONSE = 700;
    const KEY_ALREADY_PURCHASED = 585;
   
}

?>
