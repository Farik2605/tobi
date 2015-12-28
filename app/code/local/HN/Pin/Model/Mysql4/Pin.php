<?php

class HN_Pin_Model_Mysql4_Pin extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		// Note that the brands_id refers to the key field in your database table.
		$this->_init('pin/pin', 'id');
	}

	public function getPinByProductId($productId)
	{
		$read = $this->_getReadAdapter();
		echo get_class( $read->select());
		$table= $this->getTable('pin/pin');
		$select = $read->select()->from(array(
		'main_table' => $this->getTable('pin/pin') )
		,array('main_table.product_name', 'main_table.filetype') )
		->where('product_id=?', $productId);
		return $read->fetchCol($select);
	}

}