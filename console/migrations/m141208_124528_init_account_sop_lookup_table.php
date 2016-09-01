<?php

use yii\db\Schema;
use yii\db\Migration;

class m141208_124528_init_account_sop_lookup_table extends Migration
{
    public function up()
    {
        /*
        CREATE TABLE 'account_sop_lookup' (
        'id' INT(10) NOT NULL AUTO_INCREMENT,
        'account' VARCHAR(50) NULL DEFAULT NULL,
        'sop' VARCHAR(50) NULL DEFAULT NULL,
        'created' INT(10) NULL DEFAULT NULL,
        'contact' VARCHAR(50) NULL DEFAULT NULL,
        'name' VARCHAR(100) NULL DEFAULT NULL,
        'street' VARCHAR(200) NULL DEFAULT NULL,
        'town' VARCHAR(200) NULL DEFAULT NULL,
        'city' VARCHAR(200) NULL DEFAULT NULL,
        'country' VARCHAR(200) NULL DEFAULT 'GB',
        'postcode' VARCHAR(200) NULL DEFAULT NULL,
        PRIMARY KEY ('id')
        )
        COMMENT='lookup of account to sop'
        COLLATE='latin1_swedish_ci'
        ENGINE=InnoDB
        AUTO_INCREMENT=485;
        CHANGED USER_ID to ACCOUNT_ID
         *
         */

        $this->createTable('account_sop_lookup', [
            'id' => Schema::TYPE_PK,
            'account' => Schema::TYPE_STRING.'(50)',
            'sop' => Schema::TYPE_STRING.'(50)',
            'created' => Schema::TYPE_BOOLEAN,
            'contact' => Schema::TYPE_STRING.'(50)',
            'name' => Schema::TYPE_STRING.'(100)',
            'street' => Schema::TYPE_STRING.'(200)',
            'town' => Schema::TYPE_STRING.'(200)',
            'city' => Schema::TYPE_STRING.'(200)',
            'country' => Schema::TYPE_STRING.'(200) DEFAULT "GB"',
            'postcode' => Schema::TYPE_STRING.'(200)',
            ],
            'COMMENT="lookup of account to sop"'
        );
    }

    public function down()
    {
        $this->dropTable('account_sop_lookup');


    }
}
