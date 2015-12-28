<?php

class HN_Pin_Model_Itempinbuy extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pin/itempinbuy');
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