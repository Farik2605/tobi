<?php

$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_product','kontenta_corresponding_product', array (
    'group' => 'General',
    'label'    => 'Corresponding Product',
    'visible'     => true,
    'type'     => 'varchar',
    'input'    => 'text',
    'system'   => false,
    'required' => false,
    'user_defined' => 1,
));

$installer->endSetup();