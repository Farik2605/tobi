<?php
/*
 * @copyright  Copyright (c) 2015 by  CodesWholesale
 */
require_once 'vendor/autoload.php';
use CodesWholesale\CodesWholesale;

class CodesWholesale_ApiPlugin_Block_Adminhtml_Configuration_Connection_Status extends Mage_Adminhtml_Block_System_Config_Form
{
    const SANDBOX_ENV_INDEX = 0;
    const LIVE_ENV_INDEX = 1;
    const SANDBOX_CLIENT_ID = "ff72ce315d1259e822f47d87d02d261e";
    const SANDBOX_CLIENT_SECRET = '$2a$10$E2jVWDADFA5gh6zlRVcrlOOX01Q/HJoT6hXuDMJxek.YEo.lkO2T6';
    const COMPLETE = 1;
    const NOT_COMPLETE = 0;

    public function __construct()
    {
        parent::__construct();

        $this->setId('codeswholesaleApiConfigurationForm');

        $this->setTemplate('apiplugin/configuration/status.phtml');

    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'      => 'config_edit_form_codeswholesale',
            'action'  => $this->getUrl('apiplugin/adminhtml_configuration_settings/save'),
            'method'  => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();

    }

    public function connectToCw()
    {
        $cwClient = Mage::helper('apiplugin/data')->connectToCw();

        try {

            $account = $cwClient->getAccount();
            $this->setData('cwAccount', $account);

        } catch (\CodesWholesale\Resource\ResourceError $error) {

             $error->getStatus();
             $this->setData('cwError', $error);
        }
    }
}