<?php

class HN_Pin_Model_Mysql4_Orderpin_Status extends Mage_Core_Model_Mysql4_Abstract
{
    
    public function _construct()
    {    
        $this->_init('pin/orderpin_status', 'id');
    }
}