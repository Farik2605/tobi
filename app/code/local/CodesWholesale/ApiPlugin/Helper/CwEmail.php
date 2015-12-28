<?php

require_once 'vendor/autoload.php';
require_once 'Zend/Mail.php';

class CodesWholesale_ApiPlugin_Helper_CwEmail extends Mage_Core_Helper_Abstract
{
/*
 * Sends notification about PreOrdered Game
 */
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

    /*
     * Sends e-mail with information about low balance
     */
    public function notifyLowBalance($order)
    {
        $client = Mage::helper('apiplugin/data')->connectToCw()->getAccount();

        $emailTemplate = Mage::getModel('core/email_template');

        $emailTemplate->loadDefault('send_admin_info_mail');
        $emailTemplate->setTemplateSubject('CodesWholesale low balance');

        $adminData['email'] = Mage::getStoreConfig('trans_email/ident_general/email');
        $adminData['name']  =  Mage::getStoreConfig('trans_email/ident_general/name');

        $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name')); // store name
        $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email')); // store e-mail

        $emailTemplateVariables['current_balance'] =  '€' . number_format($client->getCurrentBalance(), 2, '.', '');
        $emailTemplateVariables['username'] = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
        $emailTemplateVariables['order_id'] = $order->getIncrementId();
        $emailTemplateVariables['store_name'] = $order->getStoreName();
        $emailTemplateVariables['store_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $emailTemplate->send($adminData['email'], $order->getStoreName(), $emailTemplateVariables);
    }

    /*
    * Sends e-mail with CodesWholesale Codes
    */
    public function sendCodeMail($order, $orderIncrementId, $attachments, $keys, $totalPreOrders)
{
    $emailTemplate = Mage::getModel('core/email_template')->loadDefault('send_cwcode_mail');

    $emailTemplate->setTemplateSubject('Order ID #' . $orderIncrementId . '.');

    $customerData['email'] = $order->getData('customer_email');
    $customerData['name'] = $order->getData('customer_firstname');

    $emailTemplateVariables['keys'] = $keys;
    $emailTemplateVariables['preorder'] = $totalPreOrders;

    if (count($attachments) > 0) {

        foreach ($attachments as $attachment) {

            $image = $emailTemplate->getMail()->createAttachment(file_get_contents($attachment));
            $image->type = 'image/jpeg';
            $image->disposition = Zend_Mime::DISPOSITION_INLINE;
            $image->encoding = Zend_Mime::ENCODING_BASE64;
            $image->filename = basename($attachment);
        }
    }
    $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_sales/name')); // store name
    $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_sales/email')); // store e-mail

    $emailTemplateVariables['username'] = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
    $emailTemplateVariables['order_id'] = $orderIncrementId;
    $emailTemplateVariables['store_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

    $emailTemplate->send($order->getCustomerEmail(), $order->getStoreName(), $emailTemplateVariables, $attachment);
}

    public function sendPreOrderMail($order, $attachments, $keys, $totalPreOrders)
    {
        $emailTemplate = Mage::getModel('core/email_template')->loadDefault('send_cwpreorder_mail');

        $emailTemplate->setTemplateSubject('Your PreOrdered Game. Order ID #' . $order->getIncrementId() . '.');

        $customerData['email'] = $order->getData('customer_email');
        $customerData['name'] = $order->getData('customer_firstname');

        $emailTemplateVariables['keys'] = $keys;
        $emailTemplateVariables['totalPreOrders'] = $totalPreOrders;

        if (count($attachments) > 0) {

            foreach ($attachments as $attachment) {

                $image = $emailTemplate->getMail()->createAttachment(file_get_contents($attachment));
                $image->type = 'image/jpeg';
                $image->disposition = Zend_Mime::DISPOSITION_INLINE;
                $image->encoding = Zend_Mime::ENCODING_BASE64;
                $image->filename = basename($attachment);
            }
        }

        $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_sales/name')); // store name
        $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_sales/email')); // store e-mail

        $emailTemplateVariables['username'] = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
        $emailTemplateVariables['order_id'] = $order->getIncrementId();
        $emailTemplateVariables['store_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        $emailTemplate->send($order->getCustomerEmail(), $order->getStoreName(), $emailTemplateVariables, $attachments);
    }

    /*
     * Sends stack trace or issue report to Admin
     */
    public function sendAdminErrorMail($order, $title, $orderIncrementId, $e)
    {
        $emailTemplate = Mage::getModel('core/email_template')->loadDefault('send_admin_error_mail');;

        $emailTemplate->setTemplateSubject($title);

        $adminData['email'] = $order->getData(Mage::getStoreConfig('trans_email/ident_general/name'));
        $adminData['name'] =  $order->getData(Mage::getStoreConfig('trans_email/ident_general/email'));

        $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_sales/name'));

        $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_sales/email'));

        $emailTemplateVariables['stack_trace'] = $e->getTraceAsString();
        $emailTemplateVariables['message'] = $e->getMessage();
        $emailTemplateVariables['error_class'] = get_class($e);

        $emailTemplateVariables['username'] = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();

        $emailTemplateVariables['order_id'] = $orderIncrementId;
        $emailTemplateVariables['error'] = $e;
        $emailTemplateVariables['store_name'] = $order->getStoreName();
        $emailTemplateVariables['store_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $emailTemplate->send($order->getCustomerEmail(), $order->getStoreName(), $emailTemplateVariables);
    }

    /*
    * Sends stack trace or issue about general error
    */
    public function sendAdminGeneralErrorMail($title, $e)
    {

        $emailTemplate = Mage::getModel('core/email_template')->loadDefault('send_admin_error_mail');;

        $emailTemplate->setTemplateSubject($title);

        $adminData['email'] = Mage::getStoreConfig('trans_email/ident_general/email');
        $adminData['name']  =  Mage::getStoreConfig('trans_email/ident_general/name');

        $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_sales/name'));
        $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_sales/email'));

        $emailTemplateVariables['stack_trace'] = $e->getTraceAsString();
        $emailTemplateVariables['message'] = $e->getMessage();
        $emailTemplateVariables['error_class'] = get_class($e);

        $emailTemplateVariables['username'] = "";

        $emailTemplateVariables['order_id'] = "";
        $emailTemplateVariables['error'] = $e;
        $emailTemplateVariables['store_name'] = "";
        $emailTemplateVariables['store_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        $emailTemplate->send($adminData['email'], "", $emailTemplateVariables);
    }
}