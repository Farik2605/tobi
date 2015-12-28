<?php
class HN_Pin_Model_Mysql4_Orderpin_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	public function _construct() {
		parent::_construct ();
		$this->_init ( 'pin/orderpin' );
	}
	
	/**
	 *
	 * @return array of record
	 */
	public function getPinByOrder($orderId) {
		$this->getSelect ()->where ( 'order_id=?', $orderId );
		return $this->getData ();
	}
	public function getPinByOrderItemId($orderItemId) {
		$this->getSelect ()->where ( 'order_item_id=?', $orderItemId );
		return $this->getData ();
	}
	
	/**
	 * get File pin by customer id
	 * 
	 * @return array of record
	 */
	public function getFilePinPerCs($customerId) {
		$this->getSelect ()->where ( 'customer_id=?', $customerId );
		$this->getSelect ()->where ( 'filetype !=?', HN_Pin_Model_Pin::TEXT_TYPE );
		return $this->getData ();
	}
	public function getTxtPinPerCs($customerId) {
		$this->getSelect ()->where ( 'customer_id=?', $customerId );
		$this->getSelect ()->where ( 'filetype =?', HN_Pin_Model_Pin::TEXT_TYPE );
		return $this->getData ();
	}
	public function getTextByOrder($orderId) {
		$this->getSelect ()->where ( 'order_id=?', $orderId )->where ( 'filetype = ?', 'encryptedtext' );
		return $this->getData ();
	}
	public function getFileByOrder($orderId) {
		$this->getSelect ()->where ( 'order_id=?', $orderId )->where ( 'filetype not like ?', 'encryptedtext' );
		return $this->getData ();
	}
	
	/**
	 * get File pin by customer id
	 * 
	 * @return array of record
	 */
	public function havePermission($customerId, $id) {
		$this->getSelect ()->where ( 'customer_id=?', $customerId );
		$this->getSelect ()->where ( 'id=?', $id );
		return $this->fetchItem ();
	}
}