<?php
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("
CREATE TABLE  IF NOT EXISTS {$this->getTable('orderpin')} (
`id` int(11) unsigned NOT NULL auto_increment,
`order_id` int(11) NOT NULL default 0,
`order_increment_id` varchar(50) DEFAULT NULL COMMENT 'Order Increment ID',
`order_item_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Order Item ID',
`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date of creation',
`updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Date of modification',
`customer_id` int(10) unsigned DEFAULT '0' COMMENT 'Customer ID',
`product_name` varchar(255) DEFAULT NULL COMMENT 'Product name',
`product_sku` varchar(255) DEFAULT NULL COMMENT 'Product sku',
`pin_number` varchar(255)  NULL default '',
`fileblob` LONGBLOB  NULL ,
`filetype` VARCHAR( 50 ) NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();
