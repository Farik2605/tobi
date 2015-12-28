<?php

class HN_Pin_Model_Mysql4_Orderpin_Status_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('pin/orderpin_status');
	}
	
	public function getPinByOrder($orderId) {
	
		$this->getSelect()->where('order_id=?', $orderId);
		return $this->getData();
	}
	public function getTextByOrder($orderId) {
	
		$this->getSelect()->where('order_id=?', $orderId)->where('filetype = ?' ,'encryptedtext');
		return $this->getData();
	}
	public function getFileByOrder($orderId) {
	
		$this->getSelect()->where('order_id=?', $orderId)->where('filetype != ?' ,'encryptedtext');
		return $this->getData();
	}

}