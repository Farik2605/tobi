<?php
require_once 'Zend/Mail.php';
class CodesWholesale_ApiPlugin_Mails_MageNotifyAboutPreOrder{

    public function notifyAboutPreOrder($order, $gameNames, $totalPreOrders)
    {
        $emailTemplate = Mage::getModel('core/email_template');

        $emailTemplate->loadDefault('send_admin_info_preorder_mail');
        $emailTemplate->setTemplateSubject('Information about PreOrder- Order ID: #' . $order->getIncrementId());

        $adminData['email'] =  Mage::getStoreConfig('trans_email/ident_general/email');
        $adminData['name']  =  Mage::getStoreConfig('trans_email/ident_general/name');

        $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name'));
        $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email'));

        $emailTemplateVariables['username'] = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
        $emailTemplateVariables['totalPreOrders'] = $totalPreOrders;
        $emailTemplateVariables['gameNames'] = $gameNames;

        $emailTemplateVariables['order_id'] = $order->getIncrementId();
        $emailTemplateVariables['store_name'] = $order->getStoreName();
        $emailTemplateVariables['store_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $emailTemplate->send($adminData['email'], $order->getStoreName(), $emailTemplateVariables);
    }
}