<?php
class HN_Pin_Block_Adminhtml_Order_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	public function __construct() {
		parent::__construct ();
		$this->_objectId = 'id';
		$this->_blockGroup = 'pin';
		$this->_controller = 'adminhtml_order';
	}
}