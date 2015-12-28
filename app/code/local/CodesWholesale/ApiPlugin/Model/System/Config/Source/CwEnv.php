<?php

class Codeswholesale_Apiplugin_Model_System_Config_Source_CwEnv
{

    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('apiplugin')->__('Live')),
            array('value' => 0, 'label'=>Mage::helper('apiplugin')->__('SandBox')),
        );
    }

}
