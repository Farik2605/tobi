<?php

class Codeswholesale_Apiplugin_Model_System_Config_Source_CwSpread
{

    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('apiplugin')->__('Flat')),
            array('value' => 0, 'label'=>Mage::helper('apiplugin')->__('Percent')),
        );
    }

}
