<?php

class HN_Pin_Model_Pin extends Mage_Core_Model_Abstract
{
	const STATUS_PENDING   = 'pending';
    const STATUS_AVAILABLE = 'available';
    const STATUS_EXPIRED   = 'expired';
    const STATUS_SOLD_OUT   = 'sold_out';
    const STATUS_PENDING_PAYMENT = 'pending_payment';
    const STATUS_PAYMENT_REVIEW = 'payment_review';
    
     const TEXT_TYPE= 'encryptedtext';
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('pin/pin');
    }
    
    public function getPinCollectionByProductId($productId) {
    	$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		//$table_prefix = Mage::getConfig()->getTablePrefix();
		$query='SELECT * FROM `pin`  where `product_id`='.$productId.' and `status` = 0';
		$result = $db->query($query);

		if(!$result) {
			return FALSE;
		}

		$rows = $result->fetchAll(PDO::FETCH_ASSOC);
		if (count($rows) >0 ) {
			return $rows;
		} else {
			return false;
		}
    }
    
public function getUsedPinCollectionByProductId($productId) {
    	$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		//$table_prefix = Mage::getConfig()->getTablePrefix();
		$query='SELECT * FROM `pin`  where `product_id`='.$productId.' and `status` = 1';
		$result = $db->query($query);

		if(!$result) {
			return FALSE;
		}

		$rows = $result->fetchAll(PDO::FETCH_ASSOC);
		if (count($rows) >0 ) {
			return $rows;
		} else {
			return false;
		}
    }
}