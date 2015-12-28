<?php
class HN_Pin_Block_Adminhtml_Pin_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('pin_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('pin')->__('PIN Information'));
	}
	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array('label' => Mage::helper('pin')->__('PIN Information'),'title' => Mage::helper('pin')->__('PIN Information'),'content' => $this->getLayout()->createBlock('pin/adminhtml_pin_edit_tab_form')->toHtml(),
		));
		
		return parent::_beforeToHtml();
	}
}
