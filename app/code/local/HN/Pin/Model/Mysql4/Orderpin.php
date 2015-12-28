<?php

class HN_Pin_Model_Mysql4_Orderpin extends Mage_Core_Model_Mysql4_Abstract
{
	const XML_PATH_ORDER_ITEM_STATUS = 'catalog/downloadable/order_item_status';

    const STATUS_PENDING   = 'pending';
    const STATUS_AVAILABLE = 'available';
    const STATUS_EXPIRED   = 'expired';
    const STATUS_PENDING_PAYMENT = 'pending_payment';
    const STATUS_PAYMENT_REVIEW = 'payment_review';
    
    public function _construct()
    {    
        // Note that the brands_id refers to the key field in your database table.
        $this->_init('pin/orderpin', 'id');
    }
}