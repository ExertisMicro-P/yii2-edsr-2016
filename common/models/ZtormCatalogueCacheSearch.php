<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ZtormCatalogueCache;

/**
 * ZtormCatalogueCacheSearch represents the model behind the search form about `common\models\ZtormCatalogueCache`.
 */
class ZtormCatalogueCacheSearch extends ZtormCatalogueCache
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['size', 'status', 'supplier', 'canBeOwnedMultiple', 'category', 'categoryId', 'clientId', 'defaultPurchasePriceRaw', 'DLCMasterProductId', 'DownloadDaysLimit', 'ForceAddress', 'Format', 'FormatId', 'FraudThreshold', 'FullversionProductId', 'GameLoanDays', 'GameRentalDays', 'GenreIds', 'HasAdultContent', 'HasInstallKey', 'ZId', 'InformationFull', 'InstallKeyNoWarning', 'InstallKeysOrderTimestampAssigned', 'InstallKeysOrderTimestampShared', 'IsActiveMark', 'IsAvailable', 'IsBundleOnly', 'IsCode', 'IsComingSoon', 'IsDIBSDefender', 'IsDiscontinued', 'IsDLC', 'IsDRMed', 'IsFree', 'IsLoan', 'IsLocked', 'IsMetaExternal', 'IsMetaProduct', 'IsMicrosoft', 'IsNotToBuy', 'IsOrigin', 'IsPartnerCampaign', 'IsPhysical', 'IsPrepurchase', 'IsRental', 'IsSecuROM', 'IsSecuROM_Internal', 'IsSonyDADC', 'IsSteam', 'IsTages', 'IsThruzt2', 'IsUniloc', 'IsUplay', 'IsWatermarked', 'IsZit', 'LanguageId', 'Name', 'NeedsInstallKey', 'NumberOfClicks', 'PEGI_Age_DK', 'PEGI_Age_FI', 'PEGI_Age_NO', 'PEGI_Age_Others', 'PEGI_OnlineGameplay', 'Playtime', 'PreDownloadSendKey', 'PreDownloadStartTimestamp', 'Publisher', 'RawOrdinaryPrice', 'RawPrice', 'RealProductId', 'RecommendedSalePriceRaw', 'RegistrationTimestamp', 'RemoteId', 'Requirements', 'RequiresZtormDownload', 'SId', 'SP_Id', 'StoreId', 'TotalSoldApproximative', 'UpdateTimestamp', 'lastUpdated'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ZtormCatalogueCache::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'lastUpdated' => $this->lastUpdated,
        ]);

        $query->andFilterWhere(['like', 'size', $this->size])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'supplier', $this->supplier])
            ->andFilterWhere(['like', 'canBeOwnedMultiple', $this->canBeOwnedMultiple])
            ->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['like', 'categoryId', $this->categoryId])
            ->andFilterWhere(['like', 'clientId', $this->clientId])
            ->andFilterWhere(['like', 'defaultPurchasePriceRaw', $this->defaultPurchasePriceRaw])
            ->andFilterWhere(['like', 'DLCMasterProductId', $this->DLCMasterProductId])
            ->andFilterWhere(['like', 'DownloadDaysLimit', $this->DownloadDaysLimit])
            ->andFilterWhere(['like', 'ForceAddress', $this->ForceAddress])
            ->andFilterWhere(['like', 'Format', $this->Format])
            ->andFilterWhere(['like', 'FormatId', $this->FormatId])
            ->andFilterWhere(['like', 'FraudThreshold', $this->FraudThreshold])
            ->andFilterWhere(['like', 'FullversionProductId', $this->FullversionProductId])
            ->andFilterWhere(['like', 'GameLoanDays', $this->GameLoanDays])
            ->andFilterWhere(['like', 'GameRentalDays', $this->GameRentalDays])
            ->andFilterWhere(['like', 'GenreIds', $this->GenreIds])
            ->andFilterWhere(['like', 'HasAdultContent', $this->HasAdultContent])
            ->andFilterWhere(['like', 'HasInstallKey', $this->HasInstallKey])
            ->andFilterWhere(['like', 'ZId', $this->ZId])
            ->andFilterWhere(['like', 'InformationFull', $this->InformationFull])
            ->andFilterWhere(['like', 'InstallKeyNoWarning', $this->InstallKeyNoWarning])
            ->andFilterWhere(['like', 'InstallKeysOrderTimestampAssigned', $this->InstallKeysOrderTimestampAssigned])
            ->andFilterWhere(['like', 'InstallKeysOrderTimestampShared', $this->InstallKeysOrderTimestampShared])
            ->andFilterWhere(['like', 'IsActiveMark', $this->IsActiveMark])
            ->andFilterWhere(['like', 'IsAvailable', $this->IsAvailable])
            ->andFilterWhere(['like', 'IsBundleOnly', $this->IsBundleOnly])
            ->andFilterWhere(['like', 'IsCode', $this->IsCode])
            ->andFilterWhere(['like', 'IsComingSoon', $this->IsComingSoon])
            ->andFilterWhere(['like', 'IsDIBSDefender', $this->IsDIBSDefender])
            ->andFilterWhere(['like', 'IsDiscontinued', $this->IsDiscontinued])
            ->andFilterWhere(['like', 'IsDLC', $this->IsDLC])
            ->andFilterWhere(['like', 'IsDRMed', $this->IsDRMed])
            ->andFilterWhere(['like', 'IsFree', $this->IsFree])
            ->andFilterWhere(['like', 'IsLoan', $this->IsLoan])
            ->andFilterWhere(['like', 'IsLocked', $this->IsLocked])
            ->andFilterWhere(['like', 'IsMetaExternal', $this->IsMetaExternal])
            ->andFilterWhere(['like', 'IsMetaProduct', $this->IsMetaProduct])
            ->andFilterWhere(['like', 'IsMicrosoft', $this->IsMicrosoft])
            ->andFilterWhere(['like', 'IsNotToBuy', $this->IsNotToBuy])
            ->andFilterWhere(['like', 'IsOrigin', $this->IsOrigin])
            ->andFilterWhere(['like', 'IsPartnerCampaign', $this->IsPartnerCampaign])
            ->andFilterWhere(['like', 'IsPhysical', $this->IsPhysical])
            ->andFilterWhere(['like', 'IsPrepurchase', $this->IsPrepurchase])
            ->andFilterWhere(['like', 'IsRental', $this->IsRental])
            ->andFilterWhere(['like', 'IsSecuROM', $this->IsSecuROM])
            ->andFilterWhere(['like', 'IsSecuROM_Internal', $this->IsSecuROM_Internal])
            ->andFilterWhere(['like', 'IsSonyDADC', $this->IsSonyDADC])
            ->andFilterWhere(['like', 'IsSteam', $this->IsSteam])
            ->andFilterWhere(['like', 'IsTages', $this->IsTages])
            ->andFilterWhere(['like', 'IsThruzt2', $this->IsThruzt2])
            ->andFilterWhere(['like', 'IsUniloc', $this->IsUniloc])
            ->andFilterWhere(['like', 'IsUplay', $this->IsUplay])
            ->andFilterWhere(['like', 'IsWatermarked', $this->IsWatermarked])
            ->andFilterWhere(['like', 'IsZit', $this->IsZit])
            ->andFilterWhere(['like', 'LanguageId', $this->LanguageId])
            ->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'NeedsInstallKey', $this->NeedsInstallKey])
            ->andFilterWhere(['like', 'NumberOfClicks', $this->NumberOfClicks])
            ->andFilterWhere(['like', 'PEGI_Age_DK', $this->PEGI_Age_DK])
            ->andFilterWhere(['like', 'PEGI_Age_FI', $this->PEGI_Age_FI])
            ->andFilterWhere(['like', 'PEGI_Age_NO', $this->PEGI_Age_NO])
            ->andFilterWhere(['like', 'PEGI_Age_Others', $this->PEGI_Age_Others])
            ->andFilterWhere(['like', 'PEGI_OnlineGameplay', $this->PEGI_OnlineGameplay])
            ->andFilterWhere(['like', 'Playtime', $this->Playtime])
            ->andFilterWhere(['like', 'PreDownloadSendKey', $this->PreDownloadSendKey])
            ->andFilterWhere(['like', 'PreDownloadStartTimestamp', $this->PreDownloadStartTimestamp])
            ->andFilterWhere(['like', 'Publisher', $this->Publisher])
            ->andFilterWhere(['like', 'RawOrdinaryPrice', $this->RawOrdinaryPrice])
            ->andFilterWhere(['like', 'RawPrice', $this->RawPrice])
            ->andFilterWhere(['like', 'RealProductId', $this->RealProductId])
            ->andFilterWhere(['like', 'RecommendedSalePriceRaw', $this->RecommendedSalePriceRaw])
            ->andFilterWhere(['like', 'RegistrationTimestamp', $this->RegistrationTimestamp])
            ->andFilterWhere(['like', 'RemoteId', $this->RemoteId])
            ->andFilterWhere(['like', 'Requirements', $this->Requirements])
            ->andFilterWhere(['like', 'RequiresZtormDownload', $this->RequiresZtormDownload])
            ->andFilterWhere(['like', 'SId', $this->SId])
            ->andFilterWhere(['like', 'SP_Id', $this->SP_Id])
            ->andFilterWhere(['like', 'StoreId', $this->StoreId])
            ->andFilterWhere(['like', 'TotalSoldApproximative', $this->TotalSoldApproximative])
            ->andFilterWhere(['like', 'UpdateTimestamp', $this->UpdateTimestamp]);

        return $dataProvider;
    }
}
