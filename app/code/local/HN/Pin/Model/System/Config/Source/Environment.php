<?php
/**
 * Created by PhpStorm.
 * User: Fara
 * Date: 25.01.16
 * Time: 11:35
 */

class HN_Pin_Model_System_Config_Source_Environment {
    public function toOptionArray(){
        return array(
            0 => Mage::helper('adminhtml')->__('Sandbox'),
            1 => Mage::helper('adminhtml')->__('Live environment')
        );
    }
}