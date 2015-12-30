<?php
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();
$installer->run("
ALTER TABLE  {$this->getTable('pin/pin')}  ADD  `invoice_id` VARCHAR( 45 ) NULL AFTER `pin_number`;
ALTER TABLE  {$this->getTable('orderpin')} ADD `invoice_id` VARCHAR( 45 ) NULL AFTER `pin_number`;
");
$installer->endSetup();
