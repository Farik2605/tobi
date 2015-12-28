<?php

class HN_Pin_Model_Orderpin extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pin/orderpin');
    }
    
   
}