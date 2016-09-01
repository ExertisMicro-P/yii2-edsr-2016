<?php

/**
 * Description of GetProductByIdMsgHandler
 *
 * @author rhutson
 */
namespace console\components\ZtormAPI;

use common\models\EztormCache;
use console\components\ItemPurchaser;
use console\components\ZtormAPI\APICurlRequest;

/**
 * GET PRODUCT BY ID MSG HANDLER
 * =============================
 * This allows details of a product to be downloaded from eZtorm based solely
 * on the product code.
 *
 * This is different from the similar modules such as getInstallKeysMsgHandler,
 * where the owning account details are also required, and so bases it's requests
 * around a stock item
 *
 * Class GetProductByIdMsgHandler
 *
 * @package console\components\ZtormAPI
 *
 */
class GetProductByIdMsgHandler extends APICurlRequest
{

    const CACHE_PERIOD = 1;
    const CACHE_UNIT = 'day';

    static $_cachedItem;

    private $_productID;

    /**
     * CONSTRUCTOR
     * ===========
     * Needs to be called with an object that has the eZstorm product id as
     * the property productID, currently always an ItemPurchaser
     *
     * @param \console\components\ItemPurchaser $vo
     */
    function __construct(ItemPurchaser $vo)
    {
        parent::__construct($vo);
        $this->_productID = $vo->productID;

        $className = (new \ReflectionClass($this))->getShortName();
        $stubKey   = 'StubAPI.' . $className;
        if (array_key_exists($stubKey, \Yii::$app->params)) {
            $this->_stubAPI = \Yii::$app->params[$stubKey];
        }
    }

    /**
     * GET MEG REQUEST CONTENT
     * =======================
     * This is used on APICurlRequest to insert the specific request details
     *into the overall XML request
     *
     * @return string
     */
    protected function _getMsgRequestContent()
    {
        $msg = '<Method>GetProductById</Method><Params><ProductID>' . $this->_productID . '</ProductID></Params>';

        return $msg;
    }

    /**
     * GET PRODUCT
     * ===========
     * This will check if the product has already been read, thn if not it
     * checks the cache table for a valid copy. If that is missing, it starts
     * the XML request to eZtorm and caches the result, then finally returns
     * the record for the user to use.
     *
     * @return mixed
     */
    public function getProduct()
    {
        if (self::$_cachedItem &&
            self::$_cachedItem->eztorm_id == $this->_productID
        ) {
            return self::$_cachedItem;
        }

        if (!$this->readCachedVersion()) {
            $this->sendRequest();
            $this->cacheProduct();
        }

        return self::$_cachedItem;

//        $product = $this->responseValue->Value->StoreProduct;
//
//        return $product;
    }

    /**
     * READ CACHED VERSION
     * ===================
     * This checks the eztorm_cache table for a valid cached version of the
     * requested product.
     *
     * Valid is defined as a record saved less that CACHE_PERIOD CACHE_UNIT
     * time ago.
     *
     * @return bool
     */
    private function readCachedVersion()
    {
        $a = EztormCache::find()->all();


        self::$_cachedItem = EztormCache::find()
            ->where(['eztorm_id' => $this->_productID])
            ->andWhere('created_at > NOW() - INTERVAL ' . self::CACHE_PERIOD . ' ' . self::CACHE_UNIT)
            ->one();

        if (!self::$_cachedItem) {
            EztormCache::deleteAll('eztorm_id = :id', ['id' => $this->_productID]);
            self::$_cachedItem = null;
        }

        return self::$_cachedItem;
    }

    /**
     * CACHE PRODUCT
     * =============
     * This is called after successfully reading a product details from eZtorm
     * which it saves into the eztorm_cache table. The activerecord is then
     * saved in the static _cachedItem to allow it to be directly accessed by
     * other instances of this class.
     */
    private function cacheProduct()
    {

        //$eztormRecord = $this->responseValue->Value->StoreProduct;
        $eztormRecord = $this->responseValue->StoreProduct;

        // -------------------------------------------------------------------
        // Screenshots is an array of URL items.
        // -------------------------------------------------------------------
        $sshots = [];
        if (!empty($eztormRecord->Screenshots)) {
            foreach ($eztormRecord->Screenshots->URL as $url) {
                $sshots[] = (string)($url);
            }
        }

        // -------------------------------------------------------------------
        // Not certain if there will ever be more than one genre, but is is
        // returned as a nested object, with each one having an Id and multiple
        // language name nodes, We'll only extract the uk version
        // -------------------------------------------------------------------
        $genres = [];
        if (!empty($eztormRecord->Genres)) {
            foreach ($eztormRecord->Genres->Genre as $genre) {
                $genres[] = (string)($genre->Name_EN);
            }
        }
        // -------------------------------------------------------------------
        // Cache it all
        // -------------------------------------------------------------------
        self::$_cachedItem = new EztormCache();

        self::$_cachedItem->eztorm_id = (string)$this->_productID;
        // -------------------------------------------------------------------
        // First the easy ones - simple strings or numbers
        // -------------------------------------------------------------------
        self::$_cachedItem->Name            = !empty($eztormRecord->Name) ? (string)$eztormRecord->Name : '';
        self::$_cachedItem->Category        = !empty($eztormRecord->Category) ? (string)$eztormRecord->Category : '';
        self::$_cachedItem->Format          = !empty($eztormRecord->Format) ? (string)$eztormRecord->Format : '';
        self::$_cachedItem->Publisher       = !empty($eztormRecord->Publisher) ? (string)$eztormRecord->Publisher : '';
        self::$_cachedItem->InformationFull = !empty($eztormRecord->InformationFull) ? (string)$eztormRecord->InformationFull : '';
        self::$_cachedItem->Requirements    = !empty($eztormRecord->Requirements) ? (string)$eztormRecord->Requirements : '';
        self::$_cachedItem->PEGI_Age_Others = !empty($eztormRecord->PEGI_Age_Others) ? (string)$eztormRecord->PEGI_Age_Others : '';
        self::$_cachedItem->Boxshot         = !empty($eztormRecord->Boxshot) ? (string)$eztormRecord->Boxshot->URL : '';
        self::$_cachedItem->Screenshots     = implode('^^', $sshots);
        self::$_cachedItem->Genres          = implode('^^', $genres);
        
        self::$_cachedItem->RRP             = !empty($eztormRecord->RRP->Value) ? (string)$eztormRecord->RRP->Value : '';
        self::$_cachedItem->RRPCurrency     = !empty($eztormRecord->RRP->Currency) ? (string)$eztormRecord->RRP->Currency : '';

        self::$_cachedItem->save();
    }


    /**
     * When we're stubbed, this will return something meaningful but static
     *
     * @return  SimpleXMLElement Fake Response
     */
    public function returnStub()
    {
        $xml = <<< _EOF
<?xml version="1.0" encoding="UTF-8"?>
<Response>
   <ErrorCode>0</ErrorCode>
   <ErrorMsg>Okey</ErrorMsg>
   <Value>
      <StoreProduct>
         <Boxshot>
            <URL>http://static.ztorm.net/media/tb/images/609/6092/tn_w400h300fs_1415318.jpg</URL>
         </Boxshot>
         <Genres>
            <Genre>
               <Id>63</Id>
               <Name_EN>productivity</Name_EN>
               <Name_SV>productivity</Name_SV>
               <Name_NO>productivity</Name_NO>
               <Name_DA>productivity</Name_DA>
               <Name_FI>productivity</Name_FI>
               <CategoryId>0</CategoryId>
            </Genre>
         </Genres>
         <PPRInformation>
            <minimum>
               <value>0.00</value>
               <currency>GBP</currency>
            </minimum>
            <percentage>
               <value>0</value>
               <basis>sellprice_excl_vat</basis>
            </percentage>
            <valid>
               <from>1424732400</from>
               <to />
            </valid>
         </PPRInformation>
         <RRP>
            <Value />
            <Currency>GBP</Currency>
         </RRP>
         <Screenshots>
            <URL>http://static.ztorm.net/media/tb/images/609/6092/tn_w400h300fs_1415318.jpg</URL>
            <URL>http://static.ztorm.net/media/tb/images/609/6092/tn_w400h300fs_1415318.jpg</URL>
         </Screenshots>
         <Size>0</Size>
         <Status>Active</Status>
         <Supplier>Ztorm</Supplier>
         <YoutubeTrailerID />
         <Actor />
         <AdditionalMetadata />
         <AgeLimit />
         <AudioQuality />
         <Author />
         <BBFC />
         <CanBeOwnedMultiple>true</CanBeOwnedMultiple>
         <Category>software</Category>
         <CategoryId>2</CategoryId>
         <ClientId>415</ClientId>
         <DefaultPurchasePriceRaw>0</DefaultPurchasePriceRaw>
         <Director />
         <Distributor />
         <DLCMasterProductId>0</DLCMasterProductId>
         <DownloadDaysLimit>0</DownloadDaysLimit>
         <DownloadStartApproximative />
         <DownloadStartTimestamp />
         <ESRB />
         <ForceAddress>false</ForceAddress>
         <Format>pc</Format>
         <FormatId>1</FormatId>
         <FPB />
         <FraudThreshold>0</FraudThreshold>
         <FullversionProductId>0</FullversionProductId>
         <GameLoanDays>0</GameLoanDays>
         <GameRentalDays>0</GameRentalDays>
         <GenreIds>63</GenreIds>
         <GeoRestrictions />
         <HasAdultContent>false</HasAdultContent>
         <HasInstallKey>true</HasInstallKey>
         <Id>32024</Id>
         <InformationExcerpt>Step up to the newest Office programs for growing businesses. Create and communicate faster. Access your docs from almost anywhere with Cloud Services</InformationExcerpt>
         <InformationFull>&lt;strong&gt;Top features&lt;/strong&gt;</InformationFull>
         &gt;
         <Name>Office Mac Home and Student 2011 - 1 Mac, Download (TEST DATA see returnStub()!)</Name>
      </StoreProduct>
   </Value>
</Response>
_EOF;



        $nodes = \common\components\XmlUtils::readXMLrequest($xml);

        return $nodes;
    }
}

