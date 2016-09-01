<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "account_sop_lookup".
 *
 * @property integer $id
 * @property string $account
 * @property string $sop
 * @property string $po
 * @property integer $created
 * @property string $contact
 * @property string $name
 * @property string $street
 * @property string $town
 * @property string $city
 * @property string $country
 * @property string $postcode
 */
class AccountSopLookup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account_sop_lookup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created'], 'integer'],
            [['account', 'sop','po', 'contact'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 100],
            [['street', 'town', 'city', 'country', 'postcode'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'account' => Yii::t('app', 'Account'),
            'sop' => Yii::t('app', 'Sop'),
            'po' => Yii::t('app', 'po'),
            'created' => Yii::t('app', 'Created'),
            'contact' => Yii::t('app', 'Contact'),
            'name' => Yii::t('app', 'Name'),
            'street' => Yii::t('app', 'Street'),
            'town' => Yii::t('app', 'Town'),
            'city' => Yii::t('app', 'City'),
            'country' => Yii::t('app', 'Country'),
            'postcode' => Yii::t('app', 'Postcode'),
        ];
    }
    
    /**
     * Builds and executes a SQL statement for truncating the DB table.
     * @param string $table the table to be truncated. The name will be properly quoted by the method.
     */
    public static function truncateTable()
    {
        \Yii::$app->getDb()->createCommand('SET foreign_key_checks = 0;')->execute();
        \Yii::$app->getDb()->createCommand()->truncateTable(self::tableName())->execute();
        Yii::trace('Dropped table before file processing' .__METHOD__.':'.__LINE__);
        \Yii::$app->getDb()->createCommand('SET foreign_key_checks = 1;')->execute();
    }
}
