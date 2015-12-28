<?php
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();
 $installer->run("
ALTER TABLE  {$this->getTable('pin/pin')}  ADD  `ext` VARCHAR( 20 ) NULL
");
$installer->endSetup();
