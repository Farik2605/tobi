<?php
require_once 'Zend/Mail.php';
class CodesWholesale_ApiPlugin_Mails_MageSendPreOrderMail{

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

}