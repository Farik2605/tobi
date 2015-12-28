<?php

class CodesWholesale_ApiPlugin_Helper_CwErrors extends Mage_Core_Helper_Abstract
{
   private $sendAdminErrorMail;

   private $sendAdminGeneralErrorMail;

    public function __construct($sendAdminErrorMail, $sendAdminGeneralErrorMail){

        $this->sendAdminErrorMail = $sendAdminErrorMail;
        $this->sendAdminGeneralErrorMail = $sendAdminGeneralErrorMail;
    }

    /*
     * Error support
     */
    public function supportResourceError($e, $orderIncrementId, $order)
    {

        if ($e->isInvalidToken()) {

            $this->sendAdminErrorMail->sendAdminErrorMail($order, 'Invalid token', $orderIncrementId, $e);
        } else

            // handle scenario when account's balance is not enough to make order
            if ($e->getStatus() == 400 && $e->getErrorCode() == 10002) {

                $this->sendAdminErrorMail->sendAdminErrorMail($order, 'Balance too low', $orderIncrementId, $e);
            } else
                // handle scenario when code details where not found
                if ($e->getStatus() == 404 && $e->getErrorCode() == 50002) {

                    $this->sendAdminErrorMail->sendAdminErrorMail($order, 'Code not found', $orderIncrementId, $e);
                } else
                    // handle scenario when product was not found in price list
                    if ($e->getStatus() == 404 && $e->getErrorCode() == 20001) {

                        $this->sendAdminErrorMail->sendAdminErrorMail($order, 'Product not found', $orderIncrementId, $e);
                    } else
                        // handle when quantity was less then 1
                        if ($e->getStatus() == 400 && $e->getErrorCode() == 40002) {

                            $this->sendAdminErrorMail->sendAdminErrorMail($order, 'Quantity less then 1', $orderIncrementId, $e);
                        } else {
                            $this->supportError($e, $orderIncrementId, $order);
                        }
    }

    /*
     * Support another exception's
     */
    public function supportError($e, $orderIncrementId, $order)
    {

        return $this->sendAdminGeneralErrorMail->sendAdminErrorMail($order, 'Issue' , $orderIncrementId, $e);
    }


}