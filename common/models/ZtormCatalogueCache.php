<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ztorm_catalogue_cache".
 *
 * @property integer $id
 * @property string $size
 * @property string $status
 * @property string $supplier
 * @property string $canBeOwnedMultiple
 * @property string $category
 * @property string $categoryId
 * @property string $clientId
 * @property string $defaultPurchasePriceRaw
 * @property string $DLCMasterProductId
 * @property string $DownloadDaysLimit
 * @property string $ForceAddress
 * @property string $Format
 * @property string $FormatId
 * @property string $FraudThreshold
 * @property string $FullversionProductId
 * @property string $GameLoanDays
 * @property string $GameRentalDays
 * @property string $GenreIds
 * @property string $HasAdultContent
 * @property string $HasInstallKey
 * @property string $ZId
 * @property string $InformationFull
 * @property string $InstallKeyNoWarning
 * @property string $InstallKeysOrderTimestampAssigned
 * @property string $InstallKeysOrderTimestampShared
 * @property string $IsActiveMark
 * @property string $IsAvailable
 * @property string $IsBundleOnly
 * @property string $IsCode
 * @property string $IsComingSoon
 * @property string $IsDIBSDefender
 * @property string $IsDiscontinued
 * @property string $IsDLC
 * @property string $IsDRMed
 * @property string $IsFree
 * @property string $IsLoan
 * @property string $IsLocked
 * @property string $IsMetaExternal
 * @property string $IsMetaProduct
 * @property string $IsMicrosoft
 * @property string $IsNotToBuy
 * @property string $IsOrigin
 * @property string $IsPartnerCampaign
 * @property string $IsPhysical
 * @property string $IsPrepurchase
 * @property string $IsRental
 * @property string $IsSecuROM
 * @property string $IsSecuROM_Internal
 * @property string $IsSonyDADC
 * @property string $IsSteam
 * @property string $IsTages
 * @property string $IsThruzt2
 * @property string $IsUniloc
 * @property string $IsUplay
 * @property string $IsWatermarked
 * @property string $IsZit
 * @property string $LanguageId
 * @property string $Name
 * @property string $NeedsInstallKey
 * @property string $NumberOfClicks
 * @property string $PEGI_Age_DK
 * @property string $PEGI_Age_FI
 * @property string $PEGI_Age_NO
 * @property string $PEGI_Age_Others
 * @property string $PEGI_OnlineGameplay
 * @property string $Playtime
 * @property string $PreDownloadSendKey
 * @property string $PreDownloadStartTimestamp
 * @property string $Publisher
 * @property string $RawOrdinaryPrice
 * @property string $RawPrice
 * @property string $RealProductId
 * @property string $RecommendedSalePriceRaw
 * @property string $RegistrationTimestamp
 * @property string $RemoteId
 * @property string $Requirements
 * @property string $RequiresZtormDownload
 * @property string $SId
 * @property string $SP_Id
 * @property string $StoreId
 * @property string $TotalSoldApproximative
 * @property string $UpdateTimestamp
 */
class ZtormCatalogueCache extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ztorm_catalogue_cache';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['InformationFull', 'Requirements', 'size', 'status', 'supplier', 'canBeOwnedMultiple', 'category', 'categoryId', 'clientId', 'defaultPurchasePriceRaw', 'DLCMasterProductId', 'DownloadDaysLimit', 'ForceAddress', 'Format', 'FormatId', 'FraudThreshold', 'FullversionProductId', 'GameLoanDays', 'GameRentalDays', 'GenreIds', 'HasAdultContent', 'HasInstallKey', 'ZId', 'InstallKeyNoWarning', 'InstallKeysOrderTimestampAssigned', 'InstallKeysOrderTimestampShared', 'IsActiveMark', 'IsAvailable', 'IsBundleOnly', 'IsCode', 'IsComingSoon', 'IsDIBSDefender', 'IsDiscontinued', 'IsDLC', 'IsDRMed', 'IsFree', 'IsLoan', 'IsLocked', 'IsMetaExternal', 'IsMetaProduct', 'IsMicrosoft', 'IsNotToBuy', 'IsOrigin', 'IsPartnerCampaign', 'IsPhysical', 'IsPrepurchase', 'IsRental', 'IsSecuROM', 'IsSecuROM_Internal', 'IsSonyDADC', 'IsSteam', 'IsTages', 'IsThruzt2', 'IsUniloc', 'IsUplay', 'IsWatermarked', 'IsZit', 'LanguageId', 'Name', 'NeedsInstallKey', 'NumberOfClicks', 'PEGI_Age_DK', 'PEGI_Age_FI', 'PEGI_Age_NO', 'PEGI_Age_Others', 'PEGI_OnlineGameplay', 'Playtime', 'PreDownloadSendKey', 'PreDownloadStartTimestamp', 'Publisher', 'RawOrdinaryPrice', 'RawPrice', 'RealProductId', 'RecommendedSalePriceRaw', 'RegistrationTimestamp', 'RemoteId', 'RequiresZtormDownload', 'SId', 'SP_Id', 'StoreId', 'TotalSoldApproximative', 'UpdateTimestamp', 'product_added'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'size' => 'Size',
            'status' => 'Status',
            'supplier' => 'Supplier',
            'canBeOwnedMultiple' => 'Can Be Owned Multiple',
            'category' => 'Category',
            'categoryId' => 'Category ID',
            'clientId' => 'Client ID',
            'defaultPurchasePriceRaw' => 'Default Purchase Price Raw',
            'DLCMasterProductId' => 'Dlcmaster Product ID',
            'DownloadDaysLimit' => 'Download Days Limit',
            'ForceAddress' => 'Force Address',
            'Format' => 'Format',
            'FormatId' => 'Format ID',
            'FraudThreshold' => 'Fraud Threshold',
            'FullversionProductId' => 'Fullversion Product ID',
            'GameLoanDays' => 'Game Loan Days',
            'GameRentalDays' => 'Game Rental Days',
            'GenreIds' => 'Genre Ids',
            'HasAdultContent' => 'Has Adult Content',
            'HasInstallKey' => 'Has Install Key',
            'ZId' => 'Zid',
            'InformationFull' => 'Information Full',
            'InstallKeyNoWarning' => 'Install Key No Warning',
            'InstallKeysOrderTimestampAssigned' => 'Install Keys Order Timestamp Assigned',
            'InstallKeysOrderTimestampShared' => 'Install Keys Order Timestamp Shared',
            'IsActiveMark' => 'Is Active Mark',
            'IsAvailable' => 'Is Available',
            'IsBundleOnly' => 'Is Bundle Only',
            'IsCode' => 'Is Code',
            'IsComingSoon' => 'Is Coming Soon',
            'IsDIBSDefender' => 'Is Dibsdefender',
            'IsDiscontinued' => 'Is Discontinued',
            'IsDLC' => 'Is Dlc',
            'IsDRMed' => 'Is Drmed',
            'IsFree' => 'Is Free',
            'IsLoan' => 'Is Loan',
            'IsLocked' => 'Is Locked',
            'IsMetaExternal' => 'Is Meta External',
            'IsMetaProduct' => 'Is Meta Product',
            'IsMicrosoft' => 'Is Microsoft',
            'IsNotToBuy' => 'Is Not To Buy',
            'IsOrigin' => 'Is Origin',
            'IsPartnerCampaign' => 'Is Partner Campaign',
            'IsPhysical' => 'Is Physical',
            'IsPrepurchase' => 'Is Prepurchase',
            'IsRental' => 'Is Rental',
            'IsSecuROM' => 'Is Secu Rom',
            'IsSecuROM_Internal' => 'Is Secu Rom  Internal',
            'IsSonyDADC' => 'Is Sony Dadc',
            'IsSteam' => 'Is Steam',
            'IsTages' => 'Is Tages',
            'IsThruzt2' => 'Is Thruzt2',
            'IsUniloc' => 'Is Uniloc',
            'IsUplay' => 'Is Uplay',
            'IsWatermarked' => 'Is Watermarked',
            'IsZit' => 'Is Zit',
            'LanguageId' => 'Language ID',
            'Name' => 'Name',
            'NeedsInstallKey' => 'Needs Install Key',
            'NumberOfClicks' => 'Number Of Clicks',
            'PEGI_Age_DK' => 'Pegi  Age  Dk',
            'PEGI_Age_FI' => 'Pegi  Age  Fi',
            'PEGI_Age_NO' => 'Pegi  Age  No',
            'PEGI_Age_Others' => 'Pegi  Age  Others',
            'PEGI_OnlineGameplay' => 'Pegi  Online Gameplay',
            'Playtime' => 'Playtime',
            'PreDownloadSendKey' => 'Pre Download Send Key',
            'PreDownloadStartTimestamp' => 'Pre Download Start Timestamp',
            'Publisher' => 'Publisher',
            'RawOrdinaryPrice' => 'Raw Ordinary Price',
            'RawPrice' => 'Raw Price',
            'RealProductId' => 'Real Product ID',
            'RecommendedSalePriceRaw' => 'Recommended Sale Price Raw',
            'RegistrationTimestamp' => 'Registration Timestamp',
            'RemoteId' => 'Remote ID',
            'Requirements' => 'Requirements',
            'RequiresZtormDownload' => 'Requires Ztorm Download',
            'SId' => 'Sid',
            'SP_Id' => 'Sp  ID',
            'StoreId' => 'Store ID',
            'TotalSoldApproximative' => 'Total Sold Approximative',
            'UpdateTimestamp' => 'Update Timestamp',
        ];
    }
}
