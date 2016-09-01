<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Customer;

/**
 * CustomerSearch represents the model behind the search form about `common\models\Customer`.
 */
class CustomerSearch extends Customer
{
    public function rules()
    {
        return [
            [['exertis_account_number', 'status', 'name', 'invoicing_address_line1', 'invoicing_address_line2', 'invoicing_address_line3', 'invoicing_address_line4', 'invoicing_postcode', 'invoicing_city', 'invoicing_country_code', 'delivery_address_line1', 'delivery_address_line2', 'delivery_address_line3', 'delivery_address_line4', 'delivery_postcode', 'delivery_city', 'delivery_country_code', 'vat_code', 'fixed_shipping_flag', 'fixed_shipping_charge', 'payment_terms', 'phone_number', 'shipping_method', 'unknown1', 'unknown2', 'timestamp'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Customer::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'timestamp' => $this->timestamp,
        ]);

        $query->andFilterWhere(['like', 'exertis_account_number', $this->exertis_account_number])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'invoicing_address_line1', $this->invoicing_address_line1])
            ->andFilterWhere(['like', 'invoicing_address_line2', $this->invoicing_address_line2])
            ->andFilterWhere(['like', 'invoicing_address_line3', $this->invoicing_address_line3])
            ->andFilterWhere(['like', 'invoicing_address_line4', $this->invoicing_address_line4])
            ->andFilterWhere(['like', 'invoicing_postcode', $this->invoicing_postcode])
            ->andFilterWhere(['like', 'invoicing_city', $this->invoicing_city])
            ->andFilterWhere(['like', 'invoicing_country_code', $this->invoicing_country_code])
            ->andFilterWhere(['like', 'delivery_address_line1', $this->delivery_address_line1])
            ->andFilterWhere(['like', 'delivery_address_line2', $this->delivery_address_line2])
            ->andFilterWhere(['like', 'delivery_address_line3', $this->delivery_address_line3])
            ->andFilterWhere(['like', 'delivery_address_line4', $this->delivery_address_line4])
            ->andFilterWhere(['like', 'delivery_postcode', $this->delivery_postcode])
            ->andFilterWhere(['like', 'delivery_city', $this->delivery_city])
            ->andFilterWhere(['like', 'delivery_country_code', $this->delivery_country_code])
            ->andFilterWhere(['like', 'vat_code', $this->vat_code])
            ->andFilterWhere(['like', 'fixed_shipping_flag', $this->fixed_shipping_flag])
            ->andFilterWhere(['like', 'fixed_shipping_charge', $this->fixed_shipping_charge])
            ->andFilterWhere(['like', 'payment_terms', $this->payment_terms])
            ->andFilterWhere(['like', 'phone_number', $this->phone_number])
            ->andFilterWhere(['like', 'shipping_method', $this->shipping_method])
            ->andFilterWhere(['like', 'unknown1', $this->unknown1])
            ->andFilterWhere(['like', 'unknown2', $this->unknown2]);

        return $dataProvider;
    }
}
