<?php

class HN_Pin_Model_Mysql4_Itempinbuy extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the brands_id refers to the key field in your database table.
        $this->_init('pin/itempinbuy', 'id');
    }
}