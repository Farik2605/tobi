<?php

$installer = $this;

$installer->startSetup();

$statusTable = $installer->getTable('sales/order_status');
$statusStateTable = $installer->getTable('sales/order_status_state');
$configTable = $installer->getTable('core/config_data');

$installer->run("CREATE TABLE access_tokens (
         client_config_id VARCHAR(50),
         user_id VARCHAR(255),
         scope VARCHAR(20),
         token_type VARCHAR(50),
         expires_in varchar(55),
         access_token VARCHAR(255),
         issue_time varchar(55)
         ) ");

$installer->run("CREATE TABLE refresh_tokens (
         client_config_id VARCHAR(50),
         user_id VARCHAR(255),
         scope VARCHAR(20),
         refresh_token VARCHAR(50),
         issue_time varchar(55)
         ) ");

$installer->getConnection()->insertArray($configTable, array(
    'scope',
    'scope_id',
    'path',
    'value'
    ),
    array(
        array('scope' => 'default', 'scope_id' => 0,  'path' => 'apiplugin_connection/apiplugin_group/apiplugin_env',  'value' => 0),
        array('scope' => 'default', 'scope_id' => 0,  'path' => 'apiplugin_connection/apiplugin_group/apiplugin_client_id',  'value' => '0'),
        array('scope' => 'default', 'scope_id' => 0,  'path' => 'apiplugin_connection/apiplugin_group/apiplugin_client_secret',  'value' => '0'),
        array('scope' => 'default', 'scope_id' => 0,  'path' => 'apiplugin_connection/apiplugin_group/apiplugin_balance',  'value' => 100),
        array('scope' => 'default', 'scope_id' => 0,  'path' => 'apiplugin_connection/apiplugin_group/apiplugin_spread_type',  'value' => 0),
        array('scope' => 'default', 'scope_id' => 0,  'path' => 'apiplugin_connection/apiplugin_group/apiplugin_spread_value',  'value' => 0)
));



$installer->getConnection()->insertArray($statusTable, array(
        'status',
        'label'
    ),
    array(
        array('status' => 'codeswholesale_completed', 'label' => 'Completed by CodesWholesale'),
        array('status' => 'codeswholesale_preordered', 'label' => 'PreOrdered by CodesWholesale'),
        array('status' => 'codeswholesale_failed', 'label' => 'Failed by CodesWholesale'),
    )
);

$installer->getConnection()->insertArray($statusStateTable, array(
        'status',
        'state',
        'is_default'
    ),
    array(
        array(
            'status' => 'codeswholesale_completed',
            'state' => 'codeswholesale_completed_state',
            'is_default' => 1
        ),
        array(
            'status' => 'codeswholesale_preordered',
            'state' => 'codeswholesale_preordered_state',
            'is_default' => 0
        ),
        array(
            'status' => 'codeswholesale_failed',
            'state' => 'codeswholesale_failed_state',
            'is_default' => 0
        )
    )
);


$installer->addAttribute('catalog_product', 'codeswholesale_product_id', array(
    'type'              => 'varchar(255)',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'ProductId',
    'input'             => 'text',
    'type'              => 'text',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => true,
    'filterable'        => true,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'is_configurable'   => false
));

$installer->addAttribute('catalog_product', 'codeswholesale_calculate_value', array(
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'CalculateValue',
    'input'             => 'text',
    'type'              => 'text',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => true,
    'filterable'        => true,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'is_configurable'   => false
));

$installer->getConnection()
    ->addColumn(
        $installer->getTable('sales/order_item'), 'links', 'longtext');

$installer->getConnection()
    ->addColumn(
        $installer->getTable('sales/order_item'), 'cw_order_id', 'varchar(255)');

$installer->getConnection()
    ->addColumn(
        $installer->getTable('sales/order_item'), 'number_of_preorder', 'int');

$installer->updateAttribute('catalog_product', 'codeswholesale_product_id', 'is_visible', '0');
$installer->updateAttribute('catalog_product', 'codeswholesale_calculate_value', 'is_visible', '0');



$installer->endSetup();