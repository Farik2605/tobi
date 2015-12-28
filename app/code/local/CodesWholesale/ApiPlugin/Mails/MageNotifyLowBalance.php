<?php

require_once 'Zend/Mail.php';

class CodesWholesale_ApiPlugin_Mails_MageNotifyLowBalance
{
    public function notifyLowBalance($order)
    {
        $client = Mage::helper('apiplugin/data')->connectToCw()->getAccount();

        $emailTemplate = Mage::getModel('core/email_template')->loadDefault('send_admin_info_mail');

        $emailTemplate->setTemplateSubject('CodesWholesale low balance');

        $adminData['email'] = Mage::getStoreConfig('trans_email/ident_general/email');
        $adminData['name'] = Mage::getStoreConfig('trans_email/ident_general/name');

        $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name'));
        $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email'));

        $emailTemplateVariables['current_balance'] = '€' . number_format($client->getCurrentBalance(), 2, '.', '');
        $emailTemplateVariables['username'] = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
        $emailTemplateVariables['order_id'] = $order->getIncrementId();
        $emailTemplateVariables['store_name'] = $order->getStoreName();
        $emailTemplateVariables['store_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $emailTemplate->send($adminData['email'], $order->getStoreName(), $emailTemplateVariables);
    }
}