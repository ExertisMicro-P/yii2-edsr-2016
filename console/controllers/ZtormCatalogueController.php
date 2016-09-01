<?php


namespace console\controllers;

use yii\console\Controller;

class ZtormCatalogueController extends Controller {
    
    public $cmd;
    
    public function options() {
        return ['cmd'];
    }
    
    public function optionAliases(){
        return ['c' => 'cmd'];
    }
    
    public function actionIndex($cmd = ''){
        
        
        switch ($cmd){
            case "fetchProducts":
                $this->_fetchProducts();
                break;
            
            case "lastUpdate":
                $this->_lastUpdate();
                break;
            
            case "newProducts":
                $this->_newProducts();
                break;
            
            case "setDefaultRuleForAccounts":
                $this->_setDefaultRuleForAccounts();
                break;
            
            case "help":
                $this->_showHelp();
                break;
            
            case "":
                echo "\nPlease enter a command, for help use 'help'";
                break;
            
            default:
                echo "Unknown command.";
        }
        
        echo "\n\n";
        
    }
    
    
    
    private function _showHelp(){
        
        echo "\n\nUsage:\n\n";
        
        echo "To fetch products from Ztorm to EDSR DB: 'php .\yii ztorm-catalogue fetchProducts'\n";
        echo "To check when the products were updated last, use: 'php .\yii ztorm-catalogue lastUpdate'\n";
        echo "To check the new products saved in the DB, use: 'php .\yii ztorm-catalogue newProducts'\n";
        echo "To set the default rule for all the accounts, use: 'php .\yii ztorm-catalogue setDefaultRuleForAccounts'\n\n";
        
    }
    
    
    private function _newProducts(){
        
        $newProducts = \common\models\ZtormCatalogueCache::find()->where(['product_added'=>date('Y-m-d')])->all();
        
        echo "\nProducts has been added today:\n\n";
            
        foreach($newProducts as $newProduct){
            
            echo "#".$newProduct['RealProductId']." ".$newProduct['Name']."\n";
            
        }
        
        echo "\n\nTotal: ".count($newProducts)."\n\n";
        
    }
    
    
    private function _setDefaultRuleForAccounts(){
        
        $allAccounts = \common\models\Account::find()->all();
        $saved = 0;
        $error = 0;
        
        foreach($allAccounts as $acc){
            
            if(\backend\models\AccountRuleMapping::find()->where(['account_id'=>$acc['id']])->exists()){
                echo "Could not assign ".$acc['customer_exertis_account_number']." to Default rule as it already exists. \n";
                $error++;
            } else {

                $accRuleMap = new \backend\models\AccountRuleMapping();

                $accRuleMap->account_id = $acc['id'];
                $accRuleMap->account_rule_id = 32;

                if($accRuleMap->save()){
                    echo "Default rule has been assigned to: " . $acc['customer_exertis_account_number'] . "\n";
                    $saved++;
                } else {
                    echo "Could not assign ".$acc['customer_exertis_account_number']." to Default rule. \n";
                    $error++;
                }
                
            }
            
        }
            echo "\n\n" . $saved . " has been saved and ".$error." could not be assigned. \n\n";
        
    }
    
    
    private function _lastUpdate(){
        
        $ztormLastUpdate = \common\models\ZtormCatalogueCache::find()->orderBy(['id'=>SORT_DESC])->one()->lastUpdated;
        
        $dateUpdated = explode(' ', $ztormLastUpdate)[0];
        $timeUpdated = explode(' ', $ztormLastUpdate)[1];
        
        if($dateUpdated == date('Y-m-d')){
            $dateUpdated = 'Today at';
        }
        
        
        echo "\n\n Ztorm Products has been last updated: " . $dateUpdated . ' ' . $timeUpdated . " \n";
        
    }
    
    
    private function _fetchProducts(){
        
        $store = \common\models\ZtormAccess::findOne(['type'=>'LIVE', 'storealias'=>'EDSR']);
        
        $itemPurchaser = new \console\components\ItemPurchaser();
        $itemPurchaser->setStoreDetails($store);
        
        $products = $itemPurchaser->getZtormRawCatalogue();
        
        //var_dump($products); die();
        
        $savedSuccessfully = 0;
        $updatedSuccessfully = 0;
        $SavedError = 0;
        $sshots = [];
        $genres = [];
        
        
        foreach($products as $product){
            
            //var_dump($product); die();
            
            $findProduct = \common\models\ZtormCatalogueCache::find()->where(['ZId' => $product->Id])->one();
                     
            if(!$findProduct){

                $ZtormCatalogueCache = new \common\models\ZtormCatalogueCache();

                $ZtormCatalogueCache->size = $product->Size;
                $ZtormCatalogueCache->status = $product->Status;
                $ZtormCatalogueCache->supplier = $product->Supplier;
                $ZtormCatalogueCache->canBeOwnedMultiple = $product->CanBeOwnedMultiple;
                $ZtormCatalogueCache->category = $product->Category;
                $ZtormCatalogueCache->categoryId = $product->CategoryId;
                $ZtormCatalogueCache->clientId = $product->ClientId;
                $ZtormCatalogueCache->defaultPurchasePriceRaw = $product->DefaultPurchasePriceRaw;
                $ZtormCatalogueCache->DLCMasterProductId = $product->DLCMasterProductId;
                $ZtormCatalogueCache->DownloadDaysLimit = $product->DownloadDaysLimit;
                $ZtormCatalogueCache->ForceAddress = $product->ForceAddress;
                $ZtormCatalogueCache->Format = $product->Format;
                $ZtormCatalogueCache->FormatId = $product->FormatId;
                $ZtormCatalogueCache->FraudThreshold = $product->FraudThreshold;
                $ZtormCatalogueCache->FullversionProductId = $product->FullversionProductId;
                $ZtormCatalogueCache->GameLoanDays = $product->GameLoanDays;
                $ZtormCatalogueCache->GameRentalDays = $product->GameRentalDays;
                $ZtormCatalogueCache->GenreIds = $product->GenreIds;
                $ZtormCatalogueCache->HasAdultContent = $product->HasAdultContent;
                $ZtormCatalogueCache->HasInstallKey = $product->HasInstallKey;
                $ZtormCatalogueCache->ZId = $product->Id;
                $ZtormCatalogueCache->InformationFull = $product->InformationFull;
                $ZtormCatalogueCache->InstallKeyNoWarning = $product->InstallKeyNoWarning;
                $ZtormCatalogueCache->InstallKeysOrderTimestampAssigned = $product->InstallKeysOrderTimestampAssigned;
                $ZtormCatalogueCache->InstallKeysOrderTimestampShared = $product->InstallKeysOrderTimestampShared;
                $ZtormCatalogueCache->IsActiveMark = $product->IsActiveMark;
                $ZtormCatalogueCache->IsAvailable = $product->IsAvailable;
                $ZtormCatalogueCache->IsBundleOnly = $product->IsBundleOnly;
                $ZtormCatalogueCache->IsCode = $product->IsCode;
                $ZtormCatalogueCache->IsComingSoon = $product->IsComingSoon;
                $ZtormCatalogueCache->IsDIBSDefender = $product->IsDIBSDefender;
                $ZtormCatalogueCache->IsDiscontinued = $product->IsDiscontinued;
                $ZtormCatalogueCache->IsDLC = $product->IsDLC;
                $ZtormCatalogueCache->IsDRMed = $product->IsDRMed;
                $ZtormCatalogueCache->IsFree = $product->IsFree;
                $ZtormCatalogueCache->IsLoan = $product->IsLoan;
                $ZtormCatalogueCache->IsLocked = $product->IsLocked;
                $ZtormCatalogueCache->IsMetaExternal = $product->IsMetaExternal;
                $ZtormCatalogueCache->IsMetaProduct = $product->IsMetaProduct;
                $ZtormCatalogueCache->IsMicrosoft = $product->IsMicrosoft;
                $ZtormCatalogueCache->IsNotToBuy = $product->IsNotToBuy;
                $ZtormCatalogueCache->IsOrigin = $product->IsOrigin;
                $ZtormCatalogueCache->IsPartnerCampaign = $product->IsPartnerCampaign;
                $ZtormCatalogueCache->IsPhysical = $product->IsPhysical;
                $ZtormCatalogueCache->IsPrepurchase = $product->IsPrepurchase;
                $ZtormCatalogueCache->IsRental = $product->IsRental;
                $ZtormCatalogueCache->IsSecuROM = $product->IsSecuROM;
                $ZtormCatalogueCache->IsSecuROM_Internal = $product->IsSecuROM_Internal;
                $ZtormCatalogueCache->IsSonyDADC = $product->IsSonyDADC;
                $ZtormCatalogueCache->IsSteam = $product->IsSteam;
                $ZtormCatalogueCache->IsTages = $product->IsTages;
                $ZtormCatalogueCache->IsThruzt2 = $product->IsThruzt2;
                $ZtormCatalogueCache->IsUniloc = $product->IsUniloc;
                $ZtormCatalogueCache->IsUplay = $product->IsUplay;
                $ZtormCatalogueCache->IsWatermarked = $product->IsWatermarked;
                $ZtormCatalogueCache->IsZit = $product->IsZit;
                $ZtormCatalogueCache->LanguageId = $product->LanguageId;
                $ZtormCatalogueCache->Name = $product->Name;
                $ZtormCatalogueCache->NeedsInstallKey = $product->NeedsInstallKey;
                $ZtormCatalogueCache->NumberOfClicks = $product->NumberOfClicks;
                $ZtormCatalogueCache->PEGI_Age_DK = $product->PEGI_Age_DK;
                $ZtormCatalogueCache->PEGI_Age_FI = $product->PEGI_Age_FI;
                $ZtormCatalogueCache->PEGI_Age_NO = $product->PEGI_Age_NO;
                $ZtormCatalogueCache->PEGI_Age_Others = $product->PEGI_Age_Others;
                $ZtormCatalogueCache->PEGI_OnlineGameplay = $product->PEGI_OnlineGameplay;
                $ZtormCatalogueCache->Playtime = $product->Playtime;
                $ZtormCatalogueCache->PreDownloadSendKey = $product->PreDownloadSendKey;
                $ZtormCatalogueCache->PreDownloadStartTimestamp = $product->PreDownloadStartTimestamp;
                $ZtormCatalogueCache->Publisher = $product->Publisher;
                $ZtormCatalogueCache->RawOrdinaryPrice = $product->RawOrdinaryPrice;
                $ZtormCatalogueCache->RawPrice = $product->RawPrice;
                $ZtormCatalogueCache->RealProductId = $product->RealProductId;
                $ZtormCatalogueCache->RecommendedSalePriceRaw = $product->RecommendedSalePriceRaw;
                $ZtormCatalogueCache->RegistrationTimestamp = $product->RegistrationTimestamp;
                $ZtormCatalogueCache->RemoteId = $product->RemoteId;
                $ZtormCatalogueCache->Requirements = $product->Requirements;
                $ZtormCatalogueCache->RequiresZtormDownload = $product->RequiresZtormDownload;
                $ZtormCatalogueCache->SId = $product->SId;
                $ZtormCatalogueCache->SP_Id = $product->SP_Id;
                $ZtormCatalogueCache->StoreId = $product->StoreId;
                $ZtormCatalogueCache->TotalSoldApproximative = $product->TotalSoldApproximative;
                $ZtormCatalogueCache->UpdateTimestamp = $product->UpdateTimestamp;
                $ZtormCatalogueCache->product_added = date('Y-m-d');
                $ZtormCatalogueCache->RRP = $product->RRP->Value;
                $ZtormCatalogueCache->RRPCurrency = $product->RRP->Currency;
                $ZtormCatalogueCache->Boxshot = (is_array($product->Boxshot->URL))? implode('^^', $product->Boxshot->URL) : $product->Boxshot->URL;
                $ZtormCatalogueCache->Genres = (is_array($product->Genres->Genre->Name_EN))? implode('^^', $product->Genres->Genre->Name_EN) : $product->Genres->Genre->Name_EN;
                $ZtormCatalogueCache->Screenshots = (is_array($product->Screenshots->URL))? implode('^^', $product->Screenshots->URL) : $product->Screenshots->URL;


                if($ZtormCatalogueCache->save()){
                    $msg = $this->ansiFormat($product->Name . ' (#'.$product->RealProductId.') saved successfully.', \yii\helpers\Console::FG_GREEN);
                    $savedSuccessfully++;
                } else {
                    $msg = $this->ansiFormat($product->Name . ' (#'.$product->RealProductId.') error while saving.', \yii\helpers\Console::FG_RED);
                    $SavedError++;
                }
                
            } else {
                
                $findProduct->size = $product->Size;
                $findProduct->status = $product->Status;
                $findProduct->supplier = $product->Supplier;
                $findProduct->canBeOwnedMultiple = $product->CanBeOwnedMultiple;
                $findProduct->category = $product->Category;
                $findProduct->categoryId = $product->CategoryId;
                $findProduct->clientId = $product->ClientId;
                $findProduct->defaultPurchasePriceRaw = $product->DefaultPurchasePriceRaw;
                $findProduct->DLCMasterProductId = $product->DLCMasterProductId;
                $findProduct->DownloadDaysLimit = $product->DownloadDaysLimit;
                $findProduct->ForceAddress = $product->ForceAddress;
                $findProduct->Format = $product->Format;
                $findProduct->FormatId = $product->FormatId;
                $findProduct->FraudThreshold = $product->FraudThreshold;
                $findProduct->FullversionProductId = $product->FullversionProductId;
                $findProduct->GameLoanDays = $product->GameLoanDays;
                $findProduct->GameRentalDays = $product->GameRentalDays;
                $findProduct->GenreIds = $product->GenreIds;
                $findProduct->HasAdultContent = $product->HasAdultContent;
                $findProduct->HasInstallKey = $product->HasInstallKey;
                $findProduct->ZId = $product->Id;
                $findProduct->InformationFull = $product->InformationFull;
                $findProduct->InstallKeyNoWarning = $product->InstallKeyNoWarning;
                $findProduct->InstallKeysOrderTimestampAssigned = $product->InstallKeysOrderTimestampAssigned;
                $findProduct->InstallKeysOrderTimestampShared = $product->InstallKeysOrderTimestampShared;
                $findProduct->IsActiveMark = $product->IsActiveMark;
                $findProduct->IsAvailable = $product->IsAvailable;
                $findProduct->IsBundleOnly = $product->IsBundleOnly;
                $findProduct->IsCode = $product->IsCode;
                $findProduct->IsComingSoon = $product->IsComingSoon;
                $findProduct->IsDIBSDefender = $product->IsDIBSDefender;
                $findProduct->IsDiscontinued = $product->IsDiscontinued;
                $findProduct->IsDLC = $product->IsDLC;
                $findProduct->IsDRMed = $product->IsDRMed;
                $findProduct->IsFree = $product->IsFree;
                $findProduct->IsLoan = $product->IsLoan;
                $findProduct->IsLocked = $product->IsLocked;
                $findProduct->IsMetaExternal = $product->IsMetaExternal;
                $findProduct->IsMetaProduct = $product->IsMetaProduct;
                $findProduct->IsMicrosoft = $product->IsMicrosoft;
                $findProduct->IsNotToBuy = $product->IsNotToBuy;
                $findProduct->IsOrigin = $product->IsOrigin;
                $findProduct->IsPartnerCampaign = $product->IsPartnerCampaign;
                $findProduct->IsPhysical = $product->IsPhysical;
                $findProduct->IsPrepurchase = $product->IsPrepurchase;
                $findProduct->IsRental = $product->IsRental;
                $findProduct->IsSecuROM = $product->IsSecuROM;
                $findProduct->IsSecuROM_Internal = $product->IsSecuROM_Internal;
                $findProduct->IsSonyDADC = $product->IsSonyDADC;
                $findProduct->IsSteam = $product->IsSteam;
                $findProduct->IsTages = $product->IsTages;
                $findProduct->IsThruzt2 = $product->IsThruzt2;
                $findProduct->IsUniloc = $product->IsUniloc;
                $findProduct->IsUplay = $product->IsUplay;
                $findProduct->IsWatermarked = $product->IsWatermarked;
                $findProduct->IsZit = $product->IsZit;
                $findProduct->LanguageId = $product->LanguageId;
                $findProduct->Name = $product->Name;
                $findProduct->NeedsInstallKey = $product->NeedsInstallKey;
                $findProduct->NumberOfClicks = $product->NumberOfClicks;
                $findProduct->PEGI_Age_DK = $product->PEGI_Age_DK;
                $findProduct->PEGI_Age_FI = $product->PEGI_Age_FI;
                $findProduct->PEGI_Age_NO = $product->PEGI_Age_NO;
                $findProduct->PEGI_Age_Others = $product->PEGI_Age_Others;
                $findProduct->PEGI_OnlineGameplay = $product->PEGI_OnlineGameplay;
                $findProduct->Playtime = $product->Playtime;
                $findProduct->PreDownloadSendKey = $product->PreDownloadSendKey;
                $findProduct->PreDownloadStartTimestamp = $product->PreDownloadStartTimestamp;
                $findProduct->Publisher = $product->Publisher;
                $findProduct->RawOrdinaryPrice = $product->RawOrdinaryPrice;
                $findProduct->RawPrice = $product->RawPrice;
                $findProduct->RealProductId = $product->RealProductId;
                $findProduct->RecommendedSalePriceRaw = $product->RecommendedSalePriceRaw;
                $findProduct->RegistrationTimestamp = $product->RegistrationTimestamp;
                $findProduct->RemoteId = $product->RemoteId;
                $findProduct->Requirements = $product->Requirements;
                $findProduct->RequiresZtormDownload = $product->RequiresZtormDownload;
                $findProduct->SId = $product->SId;
                $findProduct->SP_Id = $product->SP_Id;
                $findProduct->StoreId = $product->StoreId;
                $findProduct->TotalSoldApproximative = $product->TotalSoldApproximative;
                $findProduct->UpdateTimestamp = $product->UpdateTimestamp;
                $findProduct->lastUpdated = date('Y-m-d H:i:s');
                $findProduct->RRP = $product->RRP->Value;
                $findProduct->RRPCurrency = $product->RRP->Currency;
                $findProduct->Boxshot = (is_array($product->Boxshot->URL))? implode('^^', $product->Boxshot->URL) : $product->Boxshot->URL;
                $findProduct->Genres = (is_array($product->Genres->Genre->Name_EN))? implode('^^', $product->Genres->Genre->Name_EN) : $product->Genres->Genre->Name_EN;
                $findProduct->Screenshots = (is_array($product->Screenshots->URL))? implode('^^', $product->Screenshots->URL) : $product->Screenshots->URL;
                

                if($findProduct->save()){
                    $msg = $this->ansiFormat($product->Name . ' (#'.$product->RealProductId.') updated successfully.', \yii\helpers\Console::FG_GREEN);
                    $updatedSuccessfully++;
                } else {
                    $msg = $this->ansiFormat($product->Name . ' (#'.$product->RealProductId.') error while updating.', \yii\helpers\Console::FG_RED);
                    $SavedError++;
                }
                
                
            }
            
            echo $msg . "\n";
            
                    
        }
        
        echo "\n\nProduct caching is done. \n----------------------------\nNew Products: " . $savedSuccessfully . "\nUpdated Products: " . $updatedSuccessfully . "\nErrors: " . $SavedError . "\n----------------------------\n\n";
        
    }
    
    
}