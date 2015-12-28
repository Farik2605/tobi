<?php
class HN_Pin_Block_Adminhtml_Pin extends Mage_Adminhtml_Block_Widget_Grid_Container
{
public function __construct()
	{
	
		 $this->_addButton('importcsvtext', array(
            'label'     => Mage::helper('catalogrule')->__('Import text PIN '),
            'onclick'   => "location.href='".$this->getUrl('*/*/importcsvtext')."'",
            'class'     => '',
        ));
        
        
		$this->_controller = 'adminhtml_pin';
		$this->_blockGroup = 'pin';
		$this->_headerText = Mage::helper('pin')->__('Pin manager');
		$this->_addButtonLabel = Mage::helper('pin')->__('Add pin');
		parent::__construct();
		$this->_removeButton('add');
		
	}
}
