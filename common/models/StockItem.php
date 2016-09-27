<?php

namespace common\models;

use Yii;
use common\models\DigitalProduct;
use common\models\Orderdetails;
use common\models\ZtormCatalogueCache;
use common\models\Stockroom;
use exertis\savewithaudittrail\SaveWithAuditTrailBehavior;
use dosamigos\taggable\Taggable;
use common\models\Tag;

/**
 * This is the model class for table "stock_item".
 * A Stockitem is a purchased key sitting in a Stockroom.
 * It is the result of an Order (see Orderdetails).
 * It represents an instance of a DigitalProduct.
 *
 * @property integer   $id
 * @property integer   $stockroom_id
 * @property string    $productcode
 * @property integer   $eztorm_product_id
 * @property integer   $eztorm_order_id
 * @property string    $timestamp_added
 * @property string    $status
 * @property integer   $send_email
 * @property integer   $spare
 * @property Stockroom $stockroom
 * @property string    $reason
 * @property integer   $key_accessed
 *
 */
class StockItem extends \yii\db\ActiveRecord
{
    public $num;
    private $price; //used to hold price of a stock item when being purchased on ztorm
    private $po; //used to hold po of a stock item when being purchased on ztorm

    /**
     * @var string helper attribute to work with tags
     *
     * To apply tags to a StockItem, load $tagNames with a CSV string
     */
    public $tagNames;


    /**
     * Cached copy of license key
     * @var string License Key
     */
    private $key;

    /**
     * Cached copy of Download URL
     *
     * @var string Download URL
     */
    private $downloadUrl;


    /**
     * Cached copy of Product Name from eZtorm API
     *
     * @var string Product Name
     */
    private $_productName;

    /**
     * Cached copy of Publisher from eZtorm API
     *
     * @var string Publisher
     */
    private $_publisher ;

    /**
     * Cached copy of Boxshot from eZtorm API
     *
     * @var string PBoxshot
     */
    private $_boxShot;



    /**
     * STATUS CODES
     * ============
     * For efficiency and to be less error prone, these should be numeric.
     *
     * As we need to record the id of each used record in the associated
     * emailed_items or cupboard_items table, if we select them individually
     * there is a chance that the order fails due to another request grabbing
     * some of the available items.
     *
     * As a work around, we can select all records in one sql statement (inside
     * a transaction) by setting this to an unique order specific value. Assuming
     * this works, the matched entries can then be inserted into the relevant
     * table, and finally this updated to flag each entry as purchased.
     *
     * @inheritdoc
     */
    const STATUS_PURCHASED = 'PURCHASED';           // Paid from from exertis an so can be sold on
    const STATUS_NOT_PURCHASED = 'NOT PURCHASED';   // Not yet paid for, so not available for selling on
    const STATUS_DELIVERING    = '#' ;              // Prepend to the unique_id while working.
                                                    // Starts with U(sed) for alpahebtical listing purposes
    const EMAIL_SEND = 1;
    const EMAIL_SENT = 0;

    const KEY_SPARE = 1;
    const KEY_NOT_SPARE = 0;
    const KEY_REQUIRES_ATTENTION = 2; // = key now re-used
    const KEY_HIDDEN_FROM_ALL = 3; // Hide this key
    const KEY_HIDDEN_FROM_ALL_EXCEPT_RUSSELL = 4; // Hide, but let Russell see it


    public static function tableName()
    {
        return 'stock_item';
    }

    public function behaviors()
    {
        return [
            [
                'class'     => SaveWithAuditTrailBehavior::className(),
                'userClass' => '\common\models\gauth\GAUser',
            ],
            Taggable::className(),

        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['stockroom_id', 'productcode', 'eztorm_product_id', 'eztorm_order_id'], 'required'],
            [['stockroom_id', 'eztorm_product_id', 'eztorm_order_id', 'send_email', 'spare'], 'integer'],
            [['timestamp_added', 'tagNames', 'key_accessed'], 'safe'],
            [['reason'], 'string', 'max' => 100],
            [['productcode', 'status'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','StockItem ID'),
            'productcode' => Yii::t('app','Product Code'),
            'eztorm_product_id' => Yii::t('app','Eztorm Product ID'),
            'partcode'  => Yii::t('app', 'Partcode'),
            'eztorm_order_id' => Yii::t('app','Eztorm Order ID'),
            'status' => Yii::t('app','Status'),
            'send_email' =>Yii::t('app','Send Email'),
            'spare' => Yii::t('app','Spare'),
            'key_accessed' => Yii::t('app','Key Accessed'),
            'reason' => Yii::t('app', 'Reason'),
            'emailedUserName' => Yii::t('app', 'Emailed To'),
            'emailedUserAddress' => Yii::t('app', 'Email Address'),
        ];
    }


    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPo($po)
    {
        $this->po = $po;
    }

    public function getPo()
    {
        return $this->po;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockroom()
    {
        return $this->hasOne(Stockroom::className(), ['id' => 'stockroom_id']);
    }


    /* Getter for partcode  */
    public function getPartcode()
    {
        return $this->digitalProduct->partcode;
    }

    /* Getter for eztorm_order_id  */
    public function getOrderID()
    {
        return $this->eztorm_order_id;
    }

    /* Getter for description  */
    public function getDescription()
    {
        return $this->digitalProduct->description;
    }

    /* Getter for SOP  */
    public function getSop()
    {
        if (!empty($this->orderdetails)) {
           return $this->orderdetails->sop;
        }
        return null;
    }

    /* Getter for PO from OrdeeDetails  */
    public function getOrderDetailsPo()
    {
        if (!empty($this->orderdetails)) {
            return $this->orderdetails->po;
        }
        return null;
    }

    public function getOrderCleared()
    {
        return empty($this->orderdetails->filename);
    }


    public function getCustomerExertisAccountNumber()
    {
        return $this->stockroom->account->customer_exertis_account_number;
    }

    public function getCustomerName()
    {
        return $this->stockroom->account->customer->name;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['item_code' => 'productcode']);
    }

    /**
     *  Stock_item.strockroom_id -> stockroom.id
     *      stockroom.account_id -> account.id
     *          account.customer_exertis_account_number -> customer_proices.account
     *
     *
     * @return \yii\db\ActiveQuery
     */
    /*
    public function getProductPrices()
    {
        return $this->hasMany(CustomerPrice::className(), ['item_code' => 'productcode']);
    }
     *
     */


    /**
     * GET ITEM PRICE
     * ==============
     * Returns either the customer specific price or the standard price.
     * @return int
     */
    public function getItemPrice () {
        //return ($this->productPrice ? $this->productPrice->item_price : $this->product->cost_price) ;
        //$price = \common\components\CustomerPrice::getPrice($this->getCustomerExertisAccountNumber(), $this->productcode);
        //return $price;

        $dp = $this->digitalProduct;

        return $dp->getItemPrice($this->getCustomerExertisAccountNumber());
    }
/*
    public function getProductPrice()
    {
        return $this->hasOne(CustomerPrice::className(), ['item_code' => 'productcode'])
                    ->onCondition(['account' => $this->getCustomerExertisAccountNumber()]) ;
    }
 *
 */


    public function getImageUrl()
    {
        return $this->digitalProduct->getMainImage();

        return '/img/no-photo-tn.jpg';
    }


    public function getEmailedUserName () {
        return $this->emailedUser->name ;
    }

    public function getEmailedUserAddress () {
        return $this->emailedUser->email ;
    }
    public function getImageThumbnail()
    {
        return '/img/no-photo-tn.jpg';
    }


    public function getEmailedUser()
    {
        return $this->hasOne(EmailedUser::className(), ['order_number' => 'status'])->viaTable('emailed_item', ['emailed_user_id' => 'id']);
//            ->where(['NOT IN', 'status', [self::STATUS_PURCHASED, self::STATUS_NOT_PURCHASED]]) ;
    }

    public function getEmailedItem()
    {
        return $this->hasMany(EmailedItem::className(), ['stock_item_id' => 'id']);
//            ->where(['NOT IN', 'status', [self::STATUS_PURCHASED, self::STATUS_NOT_PURCHASED]]) ;
    }


    public function totalOfThisProduct()
    {
        return $this->hasMany(__CLASS__, ['productcode' => 'productcode'])
            ->onCondition(['stockroom_id' => $this->stockroom_id])
            ->count();
    }

    public function totalAvailableofThisProduct()
    {
        return $this->hasMany(__CLASS__, ['productcode' => 'productcode'])
            ->onCondition(['stockroom_id' => $this->stockroom_id])
            ->where(['status' => self::STATUS_PURCHASED])
            ->count();
    }

    /**
     *
     * @return array StockItems
     */
    static public function getStockitemstoemail()
    {
        return StockItem::find()
            ->where(['send_email' => self::EMAIL_SEND])
            ->orderBy('stockroom_id')
            ->all();
        //$this::hasMany(__CLASS__,['send_email'=>self::EMAIL_SEND])

    }


    /**
     * TOTAL OF THIS PRODUCT BY STATUS
     * ===============================
     * This will a count of the products of the current type in the
     * current stock room
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function totalOfThisProductByStatus(array $statusCodes = [])
    {
        $baseQuery = $this->hasMany(__CLASS__, ['productcode' => 'productcode'])
            ->onCondition(['stockroom_id' => $this->stockroom_id])
            ->groupBy('status')
            ->select('productcode, status, count(*) num');

        if (count($statusCodes)) {
            $baseQuery->andWhere(['in', 'status', $statusCodes]);
        }

        return $baseQuery->all();

    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDigitalProduct()
    {
        return $this->hasOne(DigitalProduct::className(), ['partcode' => 'productcode']);
    }

    public function getOrderdetails()
    {
        return $this->hasOne(Orderdetails::className(), ['stock_item_id' => 'id']);
    }

    public function getPricat(){
        return $this->hasOne(Pricat::className(), ['item_code'=>'productcode']);
    }

    public function getCompanyName()
    {
        return $this->stockroom->account->customer->name;
    }

    public function getStoreMemberID()
    {
        //stockitem has stockroom id Account has stockroom id.
        $account = $this->stockroom->account;

        //return $account->uuid;
        return $account->customer_exertis_account_number;
    }

    public function getMemberID()
    {
        $account = $this->stockroom->account;

        return isset($account->eztorm_user_id) ? $account->eztorm_user_id : '0';
    }

    public function setMemberID($id)
    {
        $account                 = $this->stockroom->account;
        $account->eztorm_user_id = $id;
        if (!$account->save()) {
            //bummer it went wrong. Do we care ?
        }
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])->viaTable('tbl_stockitem_tag_assn', ['stockitem_id' => 'id']);
    }

    public function getTagsForInput()
    {
        return implode(',', $this->tags);
    }

    /**
     * Fetches the license key for this Stock Item from eZtorm
     *
     * @return string License Key
     */
    public function getKey()
    {
        // RCH 20160816
        // Workaround for roque Stock Item which seems to be at fault
        // We keep getting an error email for this one and it may be hiding debug that we
        // desparately need in notifyCustomerofNewStockItems()
        if ($this->id == 2054) {
            return Yii::t('app', 'Key could not be fetched');
        }

        //return '';
        // have we a cached copy we can use to save us hitting the eZtorm API
        // (The key is never saved locally in the database!)
        if (empty($this->key)) {
            // no, we need to fetch it
            $this->key = \common\components\DigitalPurchaser::getProductInstallKey($this);
        }

        return !empty($this->key) ? $this->key : Yii::t('app', 'Key could not be fetched');
    } // getKey


    /**
     * Fetches the Download URL for this Stock Item from eZtorm
     *
     * @return string License Key
     */
    public function getDownloadURL()
    {
        //return '';
        // have we a cached copy we can use to save us hitting the eZtorm API
        // (The key is never saved locally in the database!)
        if (empty($this->downloadUrl)) {
            // no, we need to fetch it
            $this->downloadUrl = \common\components\DigitalPurchaser::s_getMemberFileURL($this);
        }

        return !empty($this->downloadUrl) ? $this->downloadUrl : Yii::t('app', 'Download URL could not be fetched');
    } // getDownloadURL



    /**
     * Fetches the Product name for this Stock Item from eZtorm API
     *
     * @return string Product Name
     */
    public function getProductName()
    {
        //return '';
        // have we a cached copy we can use to save us hitting the eZtorm API
        // (The key is never saved locally in the database!)
        if (empty($this->_productName)) {
            // no, we need to fetch it
            //$this->_productName = \common\components\DigitalPurchaser::s_getProductName($this);
            // RCH 20160818
            // Try to the lookup above to avoid going through so many models
            $ztorm = ZtormCatalogueCache::find()->select('Name')->where(['zId'=>$this->eztorm_product_id])->one();
            if ($ztorm) {
                $this->_productName = $ztorm->one()->Name;
            }
        }

        return !empty($this->_productName) ? $this->_productName : Yii::t('app', 'Name could not be fetched');
    } // getProductName


    /**
     * GET PUBLISHER
     * =============
     * Fetches the Product name for this Stock Item from eZtorm API
     *
     * @return string Product Name
     */
    public function getPublisher()
    {
        // -------------------------------------------------------------------
        // have we a cached copy we can use to save us hitting the eZtorm API
        // (The key is never saved locally in the database!)
        // -------------------------------------------------------------------
        if (empty($this->_publisher)) {
            $ztorm = ZtormCatalogueCache::find()->select('Publisher')->where(['zId'=>$this->eztorm_product_id])->one();
            if ($ztorm) {
                $this->_publisher = $ztorm->Publisher;
            }
        }

        return !empty($this->_publisher) ? $this->_publisher : Yii::t('app', 'Publisher Name could not be fetched');
    } // getProductName

    /**
     * Fetches the Product Boxshot image for this Stock Item from eZtorm API
     *
     * @return string Product Name
     */
    public function getBoxShotURL()
    {
        //return '';
        // have we a cached copy we can use to save us hitting the eZtorm API
        // (The key is never saved locally in the database!)
        if (empty($this->_boxShot)) {
            // no, we need to fetch it
            $url = \common\components\DigitalPurchaser::s_getBoxshot($this);
            $this->_boxShot = $url ;
        }

        return !empty($this->_boxShot) ? $this->_boxShot : 'img/no-boxshot.jpg'; //Yii::t('app', 'Boxshot could not be fetched');
    } // getBoxShotURL

}
