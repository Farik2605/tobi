<?php
/**
* HungnamEcommerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://hungnamecommerce.com/HN-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   HN
 * @package    HN_PIN
 * @version    2.0.2
 * @copyright  Copyright (c) 2012-2013 HungnamEcommerce Co. (http://hungnamecommerce.com)
 * @license    http://hungnamecommerce.com/HN-LICENSE-COMMUNITY.txt
 */



$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

CREATE TABLE  IF NOT EXISTS {$this->getTable('pin/pin')} (
`id` int(11) unsigned NOT NULL auto_increment,
`pin_number` varchar(255) NOT NULL default '',
`product_id` int(11) NOT NULL default 0,
`product_name` varchar(255) NOT NULL default '',
`file` varchar(255) NOT NULL default '',
`status` varchar(20) NOT NULL default '0',
`amount` int(11) NOT NULL default '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();
