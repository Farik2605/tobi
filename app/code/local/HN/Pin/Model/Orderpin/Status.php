<?php

class HN_Pin_Model_Orderpin_Status extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pin/orderpin_status');
    }
    public function isComplete() {
    	if ($this->getId()) {
    		if ($this->getData('delivery_status') == 1) 
    		{
    			return true;
    		}
    		
    	}
    	return false;
    }
    
   
}