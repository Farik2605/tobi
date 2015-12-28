<?php
class HN_Pin_Block_Adminhtml_Order extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_order';
		$this->_blockGroup = 'pin';
		$this->_headerText = Mage::helper('pin')->__('PIN delivery status');
		parent::__construct();
		$this->_removeButton('add');
	
	}
}