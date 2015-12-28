<?php

class HN_Pin_Model_Mysql4_Ispinproduct_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pin/ispinproduct');
    }
    
    /**
     * 
     */
    public function getProductPIN() {
		$this->getSelect()->where('status=?',1);
	     return $this;
    }
}