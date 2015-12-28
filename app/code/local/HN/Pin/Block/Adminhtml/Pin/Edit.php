<?php
class HN_Pin_Block_Adminhtml_Pin_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'pin';
		$this->_controller = 'adminhtml_pin';
		$this->_updateButton('save', 'label', Mage::helper('pin')->__('Save Pin'));
		$this->_updateButton('delete', 'label', Mage::helper('pin')->__('Delete Pin'));
		//$this->setChild('hum', '');
	}
	public function getHeaderText()
	{
		if( Mage::registry('pin_data') && Mage::registry('pin_data')->getId() ) {
		return Mage::helper('pin')->__("Edit PIN", $this->htmlEscape(Mage::registry('pin_data')->getTitle()));
	} 
	else {
		return Mage::helper('pin')->__('Add PIN');
	}
	}
}
