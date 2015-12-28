<?php
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();
 $installer->run("
ALTER TABLE  {$this->getTable('pin/orderpin')}  ADD `pin_purchased_qty` INT  NULL , ADD `pin_delivery_qty` INT  NULL, ADD `delivery_status` TINYINT NOT NULL DEFAULT '0' ;
");
 
 $installer->run("
 
 		CREATE TABLE  IF NOT EXISTS {$this->getTable('pin/orderpin_status')} (
 		`id` int(11) unsigned NOT NULL auto_increment,
 		`order_id`  int(11) NOT NULL default '0',
 		`item_id`  int(11) NOT NULL default '0',
 		`product_id` int(11) NOT NULL default 0,
 		`product_name` varchar(255) NOT NULL default '',
 		`product_sku` varchar(255) NOT NULL default '',
 		`delivery_status`  int(11) NOT NULL default '0',
 		`total_qty` int(11) NOT NULL default '0',
 		`delivery_qty` int(11) NOT NULL default '0',
 		PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 ");
$installer->endSetup();
