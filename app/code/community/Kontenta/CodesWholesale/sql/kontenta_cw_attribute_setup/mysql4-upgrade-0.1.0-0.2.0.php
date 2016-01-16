<?php

$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_product','kontenta_cw_sync_price', array (
    'group' => 'General',
    'label'    => 'CW sync price',
    'visible'     => false,
    'type'     => 'varchar',
    'default' => '1',
    'input'    => 'text',
    'system'   => false,
    'required' => false,
    'user_defined' => 1,
));

$installer->addAttribute('catalog_product','kontenta_cw_sync_stock', array (
    'group' => 'General',
    'label'    => 'CW sync stock',
    'visible'     => false,
    'type'     => 'varchar',
    'default' => '1',
    'input'    => 'text',
    'system'   => false,
    'required' => false,
    'user_defined' => 1,
));

$installer->addAttribute('catalog_product','kontenta_cw_preorder', array (
    'group' => 'General',
    'label'    => 'CW if preorder',
    'visible'     => false,
    'type'     => 'varchar',
    'default' => '0',
    'input'    => 'text',
    'system'   => false,
    'required' => false,
    'user_defined' => 1,
));

$installer->endSetup();