<?php

use yii\db\Schema;
use yii\db\Migration;

class m141203_153329_init_customer_table extends Migration
{
    public function up()
    {
    	/*
    	CREATE TABLE IF NOT EXISTS `yii2-edsr2`.`customer` (
  `exertis_account_number` VARCHAR(20) NOT NULL,
  `status` VARCHAR(1) NULL DEFAULT NULL,
  `name` VARCHAR(255) NULL DEFAULT NULL,
  `invoicing_address_line1` VARCHAR(240) NULL DEFAULT NULL,
  `invoicing_address_line2` VARCHAR(240) NULL DEFAULT NULL,
  `invoicing_address_line3` VARCHAR(240) NULL DEFAULT NULL,
  `invoicing_address_line4` VARCHAR(240) NULL DEFAULT NULL,
  `invoicing_postcode` VARCHAR(60) NULL DEFAULT NULL,
  `invoicing_city` VARCHAR(60) NULL DEFAULT NULL,
  `invoicing_country_code` VARCHAR(10) NULL DEFAULT NULL,
  `delivery_address_line1` VARCHAR(240) NULL DEFAULT NULL,
  `delivery_address_line2` VARCHAR(240) NULL DEFAULT NULL,
  `delivery_address_line3` VARCHAR(240) NULL DEFAULT NULL,
  `delivery_address_line4` VARCHAR(240) NULL DEFAULT NULL,
  `delivery_postcode` VARCHAR(60) NULL DEFAULT NULL,
  `delivery_city` VARCHAR(60) NULL DEFAULT NULL,
  `delivery_country_code` VARCHAR(10) NULL DEFAULT NULL,
  `vat_code` VARCHAR(20) NULL DEFAULT NULL,
  `fixed_shipping_flag` VARCHAR(1) NULL DEFAULT NULL,
  `fixed_shipping_charge` VARCHAR(20) NULL DEFAULT NULL,
  `payment_terms` VARCHAR(1) NULL DEFAULT NULL,
  `phone_number` VARCHAR(45) NULL DEFAULT NULL,
  `shipping_method` VARCHAR(45) NULL DEFAULT NULL,
  `unknown1` VARCHAR(45) NULL DEFAULT NULL,
  `unknown2` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`exertis_account_number`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
		*/

		$this->createTable('customer', [
            'exertis_account_number' => Schema::TYPE_STRING."(20)  NOT NULL PRIMARY KEY",
            'status' => Schema::TYPE_STRING."(1)",
            'name' => Schema::TYPE_INTEGER."(255)",
            'invoicing_address_line1' => Schema::TYPE_STRING."(240)",
            'invoicing_address_line2' => Schema::TYPE_STRING."(240)",
            'invoicing_address_line3' => Schema::TYPE_STRING."(240)",
            'invoicing_address_line4' => Schema::TYPE_STRING."(240)",
            'invoicing_postcode' => Schema::TYPE_STRING."(60)",
            'invoicing_city' => Schema::TYPE_STRING."(60)",
            'invoicing_country_code' => Schema::TYPE_STRING."(10)",
            'delivery_address_line1' => Schema::TYPE_STRING."(240)",
            'delivery_address_line2' => Schema::TYPE_STRING."(240)",
            'delivery_address_line3' => Schema::TYPE_STRING."(240)",
            'delivery_address_line4' => Schema::TYPE_STRING."(240)",
            'delivery_postcode' => Schema::TYPE_STRING."(60)",
            'delivery_city' => Schema::TYPE_STRING."(60)",
            'delivery_country_code' => Schema::TYPE_STRING."(10)",
            'vat_code' => Schema::TYPE_STRING."(20)",
            'fixed_shipping_flag' => Schema::TYPE_STRING."(1)",
            'fixed_shipping_charge' => Schema::TYPE_STRING."(20)",
            'payment_terms' => Schema::TYPE_STRING."(1)",
            'phone_number' => Schema::TYPE_STRING."(45)",
            'shipping_method' => Schema::TYPE_STRING."(45)",
            'unknown1' => Schema::TYPE_STRING."(45)",
            'unknown2' => Schema::TYPE_STRING."(45)",
            'timestamp' => Schema::TYPE_TIMESTAMP.' DEFAULT CURRENT_TIMESTAMP',

        ]);


    }

    public function down()
    {
        $this->dropTable('customer');


    }
}
