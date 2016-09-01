<?php

use yii\db\Schema;
use yii\db\Migration;

class m160316_132956_create_ztorm_catalogue_cache extends Migration
{
    public function up()
    {

        $columns = [
            'id' => $this->primaryKey(),
            'size' => $this->string(),
            'status' => $this->string(),
            'supplier' => $this->string(),
            'canBeOwnedMultiple' => $this->string(),
            'category' => $this->string(),
            'categoryId' => $this->string(),
            'clientId' => $this->string(),
            'defaultPurchasePriceRaw' => $this->string(),
            'DLCMasterProductId' => $this->string(),
            'DownloadDaysLimit' => $this->string(),
            'ForceAddress' => $this->string(),
            'Format' => $this->string(),
            'FormatId' => $this->string(),
            'FraudThreshold' => $this->string(),
            'FullversionProductId' => $this->string(),
            'GameLoanDays' => $this->string(),
            'GameRentalDays' => $this->string(),
            'GenreIds' => $this->string(),
            'HasAdultContent' => $this->string(),
            'HasInstallKey' => $this->string(),
            'ZId' => $this->string(),
            'InformationFull' => $this->text(),
            'InstallKeyNoWarning' => $this->string(),
            'InstallKeysOrderTimestampAssigned' => $this->string(),
            'InstallKeysOrderTimestampShared' => $this->string(),
            'IsActiveMark' => $this->string(),
            'IsAvailable' => $this->string(),
            'IsBundleOnly' => $this->string(),
            'IsCode' => $this->string(),
            'IsComingSoon' => $this->string(),
            'IsDIBSDefender' => $this->string(),
            'IsDiscontinued' => $this->string(),
            'IsDLC' => $this->string(),
            'IsDRMed' => $this->string(),
            'IsFree' => $this->string(),
            'IsLoan' => $this->string(),
            'IsLocked' => $this->string(),
            'IsMetaExternal' => $this->string(),
            'IsMetaProduct' => $this->string(),
            'IsMicrosoft' => $this->string(),
            'IsNotToBuy' => $this->string(),
            'IsOrigin' => $this->string(),
            'IsPartnerCampaign' => $this->string(),
            'IsPhysical' => $this->string(),
            'IsPrepurchase' => $this->string(),
            'IsRental' => $this->string(),
            'IsSecuROM' => $this->string(),
            'IsSecuROM_Internal' => $this->string(),
            'IsSonyDADC' => $this->string(),
            'IsSteam' => $this->string(),
            'IsTages' => $this->string(),
            'IsThruzt2' => $this->string(),
            'IsUniloc' => $this->string(),
            'IsUplay' => $this->string(),
            'IsWatermarked' => $this->string(),
            'IsZit' => $this->string(),
            'LanguageId' => $this->string(),
            'Name' => $this->string(),
            'NeedsInstallKey' => $this->string(),
            'NumberOfClicks' => $this->string(),
            'PEGI_Age_DK' => $this->string(),
            'PEGI_Age_FI' => $this->string(),
            'PEGI_Age_NO' => $this->string(),
            'PEGI_Age_Others' => $this->string(),
            'PEGI_OnlineGameplay' => $this->string(),
            'Playtime' => $this->string(),
            'PreDownloadSendKey' => $this->string(),
            'PreDownloadStartTimestamp' => $this->string(),
            'Publisher' => $this->string(),
            'RawOrdinaryPrice' => $this->string(),
            'RawPrice' => $this->string(),
            'RealProductId' => $this->string(),
            'RecommendedSalePriceRaw' => $this->string(),
            'RegistrationTimestamp' => $this->string(),
            'RemoteId' => $this->string(),
            'Requirements' => $this->text(),
            'RequiresZtormDownload' => $this->string(),
            'SId' => $this->string(),
            'SP_Id' => $this->string(),
            'StoreId' => $this->string(),
            'TotalSoldApproximative' => $this->string(),
            'UpdateTimestamp' => $this->string(),
        ];
        
        $this->createTable('ztorm_catalogue_cache', $columns);
        
    }

    public function down()
    {
        $this->dropTable('ztorm_catalogue_cache');
    }
}
