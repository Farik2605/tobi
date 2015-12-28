<?php

class HN_Pin_Model_Mysql4_Ispinproduct extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the brands_id refers to the key field in your database table.
        $this->_init('pin/ispinproduct', 'id');
    }
    
    /**
	 * get pin type
	 * @return array
	 */
	public function getPinTypeByProductId($productId)
	{
		$read = $this->_getReadAdapter();
		
		$select = $read->select()->from(array(
		'main_table' => $this->getMainTable() )
		,array('main_table.type') )
		->where('product_id=?', $productId);
		return $read->fetchCol($select);
	}
    
}