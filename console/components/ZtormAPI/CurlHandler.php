<?php

/**
 * Description of CurlManager
 * Handles the sending of the xml requests and return the response.
 * @author helenk
 */
namespace console\components\ZtormAPI;
use console\components\FileFeedErrorCodes;
use console\components\ZtormAPI\CurlException;
use \Yii;
class CurlHandler {
    //put your code here
    
        static function sendXml($xml, $url, $waitresponse, $xmloutscheme=null, $xmlinschemepath=null){
           Yii::info(": sendcurl" . $xml. ' url ' . $url,__METHOD__ );
        
      //     $ch = curl_init();
       
        // Set URL on which you want to post the Form and/or data
       // curl_setopt($ch, CURLOPT_URL, $url);
        // Data+Files to be posted
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        // Pass TRUE or 1 if you want to wait for and catch the response against the request made
       // curl_setopt($ch, CURLOPT_RETURNTRANSFER, $waitresponse);
        // For Debug mode; shows up any error encountered during the operation
        //curl_setopt($ch, CURLOPT_VERBOSE, 1);
        
       // curl_setopt ($ch, CURLOPT_CAINFO, "/etc/curl-ca-crts/cacert.pem");
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // RCH 20140903
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // RCH 20140903 this seems to cure the intranet2/intrnate4 curl differences
        //curl_setopt($ch, CURLOPT_POST, 1);  // RCH 20140903
        
        // Execute the request
        //$response = curl_exec($ch);
       // return $response;
        
        
       /// }
        
        //XmlUtils::checkXmlAgainstSchema ($new, $xmlinschemepath) ;
        
   $options = array (CURLOPT_RETURNTRANSFER => true, // return web page
    CURLOPT_HEADER => false, // don't return headers
    CURLOPT_FOLLOWLOCATION => true, // follow redirects
    CURLOPT_ENCODING => "", // handle compressed
    CURLOPT_USERAGENT => "exertis", // who am i
    CURLOPT_AUTOREFERER => true, // set referer on redirect
    CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
    CURLOPT_TIMEOUT => 120, // timeout on response
    CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
    CURLOPT_URL => $url,
    CURLOPT_POSTFIELDS => $xml,
    CURLOPT_RETURNTRANSFER => $waitresponse,
    CURLOPT_VERBOSE => 1,
    CURLOPT_SSL_VERIFYPEER => 0
    // curl_setopt ($ch, CURLOPT_CAINFO, "/etc/curl-ca-crts/cacert.pem");
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // RCH 20140903
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // RCH 20140903 this seems to cure the intranet2/intrnate4 curl differences
        //curl_setopt($ch, CURLOPT_POST, 1);  // RCH 20140903
       );

    $ch = curl_init ( $url );
    curl_setopt_array ( $ch, $options );
    $content = curl_exec ( $ch );
    $err = curl_errno ( $ch );
    $errmsg = curl_error ( $ch );
   
    $header = curl_getinfo ( $ch );
    $httpCode = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
    curl_close ( $ch );
    if($err > 0 ){ //we have failed
        $msg = ": Error sendcurl ". $err . ' xmlRequest ' . $xml. ' url ' . $url . ' message ' . $errmsg.  __METHOD__;
         Yii::error($msg);
         throw new CurlException($err,'$errmsg');
    }


    
    $header ['errno'] = $err;
    $header ['errmsg'] = $errmsg;
    $header ['content'] = $content;
    if (strpos($xml,'GetProductById')!==FALSE){
        //return self::_fakeError(); // with fakebad we see garbage on the screen
        //return self::_fakeGood();  // with fakegood, we get a good display
        // with no fake returns we retrn the real value but get garbage to the screen
    }
    return $header ['content'];
        }
        

/*
        private static function _fakeGood() {
            $s = <<<EOT
<Response><ErrorCode>0</ErrorCode><ErrorMsg>Okey</ErrorMsg><Value><StoreProduct><Genres><Genre><Id>63</Id><Name_EN>productivity</Name_EN><Name_SV>productivity</Name_SV><Name_NO>productivity</Name_NO><Name_DA>productivity</Name_DA><Name_FI>productivity</Name_FI><CategoryId>0</CategoryId></Genre><Genre><Id>64</Id><Name_EN>word processing</Name_EN><Name_SV>word processing</Name_SV><Name_NO>word processing</Name_NO><Name_DA>word processing</Name_DA><Name_FI>word processing</Name_FI><CategoryId>0</CategoryId></Genre></Genres><PPRInformation><minimum><value>0</value><currency>GBP</currency></minimum><percentage><value>0</value><basis>sellprice_excl_vat</basis></percentage><valid><from>1443015180</from><to></to></valid></PPRInformation><RRP><Value></Value><Currency></Currency></RRP><Screenshots><URL>http://static.ztorm.net/media/images/609/6095/609582.png</URL><URL>http://static.ztorm.net/media/images/609/6095/609583.png</URL><URL>http://static.ztorm.net/media/images/609/6095/609584.png</URL><URL>http://static.ztorm.net/media/images/609/6095/609585.png</URL><URL>http://static.ztorm.net/media/images/609/6095/609586.png</URL><URL>http://static.ztorm.net/media/images/609/6095/609587.png</URL><URL>http://static.ztorm.net/media/images/609/6095/609588.png</URL><URL>http://static.ztorm.net/media/images/609/6095/609589.png</URL><URL>http://static.ztorm.net/media/images/609/6095/609590.png</URL><URL>http://static.ztorm.net/media/images/609/6095/609591.png</URL><URL>http://static.ztorm.net/media/images/609/6095/609592.png</URL><URL>http://static.ztorm.net/media/images/609/6095/609593.png</URL><URL>http://static.ztorm.net/media/images/609/6095/609594.png</URL></Screenshots><Size>0</Size><Status>active</Status><Supplier>Ztorm</Supplier><YoutubeTrailerID></YoutubeTrailerID><Actor></Actor><AdditionalMetadata></AdditionalMetadata><AgeLimit></AgeLimit><AudioQuality></AudioQuality><Author></Author><BBFC></BBFC><CanBeOwnedMultiple>true</CanBeOwnedMultiple><Category>software</Category><CategoryId>2</CategoryId><ClientId>415</ClientId><DefaultPurchasePriceRaw>0</DefaultPurchasePriceRaw><Director></Director><Distributor></Distributor><DLCMasterProductId>0</DLCMasterProductId><DownloadDaysLimit>0</DownloadDaysLimit><DownloadStartApproximative></DownloadStartApproximative><DownloadStartTimestamp></DownloadStartTimestamp><ESRB></ESRB><ForceAddress>false</ForceAddress><Format>multi</Format><FormatId>4</FormatId><FPB></FPB><FraudThreshold>0</FraudThreshold><FullversionProductId>0</FullversionProductId><GameLoanDays>0</GameLoanDays><GameRentalDays>0</GameRentalDays><GenreIds>63,64</GenreIds><GeoRestrictions></GeoRestrictions><HasAdultContent>false</HasAdultContent><HasInstallKey>true</HasInstallKey><Id>32315</Id><InformationExcerpt>Experience Office your way—up to date, customized, and accessible from the cloud.</InformationExcerpt><InformationFull>Experience the Office that roams with you. Your documents, programs, and settings are accessible in the cloud, freeing you to create, share, and connect however you work best.

&lt;strong&gt;The newest from Office.&lt;/strong&gt;
Office 365 Home Premium has the latest version of the applications you know and love, plus cloud services so you can have Office when and where you need it. Just sign in and you can get to your Office files, applications, and settings from virtually anywhere.

The latest versions of all our best-in-class applications plus cloud services including Skype and SkyDrive on up to 5 PCs, Windows 8 tablets, and Macs.1

&lt;strong&gt;What it includes:&lt;/strong&gt;
•	Office for the entire household on up to 5 PCs and Windows 8 tablets, Macs.1
•	The latest versions of: Word, Excel, PowerPoint, Outlook, OneNote,2 Publisher,2 and Access.2
•	An extra 20 GB of online storage in SkyDrive (27 GB total) for anywhere access to your documents.3
•	60 minutes of Skype™ calls each month to phones in 40+ countries.4
•	One convenient annual subscription for the whole household with automatic upgrades included so you’re always up to date with the latest features and services.

&lt;strong&gt;Now your Office is there whenever you need it.&lt;/strong&gt;
•	Be more productive with a full version of Office, no matter where you are.
•	Sign in to get Office on your PC and Windows 8 tablet, Mac.1
•	Each user can sign in to their Microsoft account to get to their documents, applications, and settings.
•	You’ll always have the latest features and services, thanks to automatic version upgrades.

&lt;strong&gt;Sharing and communicating is easier. &lt;/strong&gt;
•	Get all the latest email, scheduling, and task tools for the entire household.
•	Use OneNote to capture and share notes, pictures, web pages, voice memos, and more.2
•	Allow others to read and scroll through your Word docs in real time through a browser, even if they don’t have Word.

&lt;strong&gt;Your favorite applications are smarter, too.&lt;/strong&gt;
•	Incorporate content from PDFs into Word documents quickly and easily.
•	Add pictures, videos, or online media to your Word documents with a simple drag and drop.
•	Create more visually compelling presentations with widescreen themes in PowerPoint.
•	Find meaning in numbers faster with the Quick Analysis and Chart Animations in Excel.

&lt;strong&gt;What’s Office 365?&lt;/strong&gt;
Office 365 has the latest version of the applications you know and love, plus cloud services so you can have Office when and where you need it. Just sign in and you can get to your Office files, applications, and settings from virtually anywhere.

Office 365 comes as an annual subscription. Microsoft and your Office retailer will let you know when it’s time to renew.</InformationFull><InstallKeyNoWarning>0</InstallKeyNoWarning><InstallKeysOrderTimestampAssigned>0</InstallKeysOrderTimestampAssigned><InstallKeysOrderTimestampShared>0</InstallKeysOrderTimestampShared><IsActiveMark>false</IsActiveMark><IsAvailable>true</IsAvailable><ISBN10></ISBN10><ISBN13></ISBN13><IsBundleOnly>false</IsBundleOnly><IsCode>true</IsCode><IsComingSoon>false</IsComingSoon><IsDIBSDefender>false</IsDIBSDefender><IsDiscontinued>false</IsDiscontinued><IsDLC>false</IsDLC><IsDRMed>false</IsDRMed><IsFree>false</IsFree><IsLoan>false</IsLoan><IsLocked>false</IsLocked><IsMetaExternal>false</IsMetaExternal><IsMetaProduct>false</IsMetaProduct><IsMicrosoft>true</IsMicrosoft><IsNotToBuy>false</IsNotToBuy><IsOrigin>false</IsOrigin><IsPartnerCampaign>false</IsPartnerCampaign><IsPhysical>false</IsPhysical><IsPrepurchase>false</IsPrepurchase><IsRental>false</IsRental><IsSecuROM>false</IsSecuROM><IsSecuROM_Internal>false</IsSecuROM_Internal><IsSonyDADC>false</IsSonyDADC><IsSteam>false</IsSteam><IsTages>false</IsTages><IsThruzt2>false</IsThruzt2><IsUniloc>false</IsUniloc><IsUplay>false</IsUplay><IsWatermarked>false</IsWatermarked><IsZit>false</IsZit><Keywords></Keywords><LanguageId>1</LanguageId><ListUpdateTimestamp></ListUpdateTimestamp><Name>Office 365 Home Premium – 5 PCs or Macs - 1 year – Download</Name><NeedsInstallKey>true</NeedsInstallKey><NumberOfClicks>0</NumberOfClicks><OrdinaryPrice><Value>0.00</Value><Currency>GBP</Currency></OrdinaryPrice><Partner></Partner><PEGI_Age_DK>0</PEGI_Age_DK><PEGI_Age_FI>0</PEGI_Age_FI><PEGI_Age_NO>0</PEGI_Age_NO><PEGI_Age_Others>0</PEGI_Age_Others><PEGI_Age_SE>0</PEGI_Age_SE><PEGI_Content_list></PEGI_Content_list><PEGI_OnlineGameplay>0</PEGI_OnlineGameplay><Playtime>0</Playtime><Points></Points><PreDownloadSendKey>0</PreDownloadSendKey><PreDownloadStartTimestamp>0</PreDownloadStartTimestamp><Price><Value>0.00</Value><Currency>GBP</Currency></Price><PublicationYear></PublicationYear><PublishedTimestamp></PublishedTimestamp><Publisher>Microsoft</Publisher><Quality></Quality><RawOrdinaryPrice>0</RawOrdinaryPrice><RawPrice>0</RawPrice><Reader></Reader><RealProductId>32315</RealProductId><RecommendedSalePriceRaw>0</RecommendedSalePriceRaw><RegistrationTimestamp>1426893164</RegistrationTimestamp><RemoteId>0</RemoteId><RemoteTextId>6GQ-00092</RemoteTextId><Requirements>PC
Operating System: Windows® 7 or Windows 8, 32- or 64-bit OS only
Computer and Processor: 1 GHz processor with SSE2 support
Memory: 2 GB RAM
Hard Disk: 3 GB available hard disk space
Display: 1366 x 768 screen resolution
Mac
Computer and Processor: Intel processor
Operating System: Mac OS X version 10.6
Memory: 1 GB RAM
Hard Disk: 2.5 GB available hard disk space; HFS+ hard disk format
Display: 1280 x 800 screen resolution

Additional Requirements
Microsoft Internet Explorer 8, 9, or 10; Mozilla Firefox 10.x or a later version; Apple Safari 5; or Google Chrome 17.x.

Internet connection. Fees may apply.

Microsoft and Skype accounts

A touch-enabled device is required to use any multi-touch functionality. However, all features and functionality are always available by using a keyboard, mouse, or other standard or accessible input device. New touch features are optimized for use with Windows 8.

Product functionality and graphics may vary based on your system configuration. Some features may require additional or advanced hardware or server connectivity.

1 Windows 7, Windows 8 OS, Windows Phone 7.5, Mac OS X version 10.6.0 required. Windows RT devices come preinstalled with Office Home &amp; Student 2013 RT Preview. Visit www.office.com/mobile for applicable devices. Internet connection required. Internet and mobile telephone usage charges may apply.
2Access and Publisher available on PC only.  OneNote not available on Mac OS.
3Internet and/or carrier network connectivity required; charges may apply.
4See office.com/information for details. Skype account required. Excludes special, premium, and non-geographic numbers. Calls to mobile phones are for select countries only. Skype available only in select countries.</Requirements><RequiresZtormDownload>false</RequiresZtormDownload><SId>0</SId><SP_Id>0</SP_Id><StoreId>183</StoreId><TotalSoldApproximative>0</TotalSoldApproximative><UpdateTimestamp>1429709521</UpdateTimestamp><USK></USK><BundledProducts></BundledProducts><Boxshot><URL>http://static.ztorm.net/media/images/609/6095/609581.jpg</URL></Boxshot></StoreProduct></Value></Response>

EOT;

                return $s;
        }
*/


/*
private static function _fakeError() {
    $s = <<<EOT
<Response><ErrorCode>1001</ErrorCode><ErrorMsg>Product not found</ErrorMsg></Response>
EOT;
    return $s;
}
*/

}
