<?php
class HN_Pin_Block_Adminhtml_Pin_Import extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'pin';
		$this->_controller = 'adminhtml_pin';
		//adminhtml_giftcert_edit_form
		//$this->addChild('mm','giftcert/adminhtml_giftcert_edit_form');
		$this->removeButton("save");
		//$this->_updateButton('save', 'label', Mage::helper('giftcert')->__('Import') , 'onclick' , 'import_form.submit()');
		//$this->_updateButton('delete', 'label', Mage::helper('giftcert')->__('Delete Giftcert'));
		 $this->_addButton('import', array(
            'label'     => Mage::helper('adminhtml')->__('Import'),
            'onclick'   => "document.getElementById('import_form').submit();",
            'class'     => 'import',
        ), 1);
	}
	public function getHeaderText()
	{
		
		return Mage::helper('pin')->__('Import');
	
	}
}

