<?php

require_once 'Zend/Mail.php';

class CodesWholesale_ApiPlugin_Mails_MageSendCodeMail{

    public function sendCodeMail($order, $attachments, $keys, $totalPreOrders)
    {
        $orderIncrementId = $order->getIncrementId();
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
}