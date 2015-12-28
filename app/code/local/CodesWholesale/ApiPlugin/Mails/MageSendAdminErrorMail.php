<?php
require_once 'Zend/Mail.php';

class CodesWholesale_ApiPlugin_Mails_MageSendAdminErrorMail
{
    public function sendAdminErrorMail($order, $title, $e)
    {
        $orderIncrementId = $order->getIncerementId();
        $emailTemplate = Mage::getModel('core/email_template')->loadDefault('send_admin_error_mail');;

        $emailTemplate->setTemplateSubject($title);

        $adminData['email'] = $order->getData(Mage::getStoreConfig('trans_email/ident_general/name'));
        $adminData['name'] = $order->getData(Mage::getStoreConfig('trans_email/ident_general/email'));

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
}
