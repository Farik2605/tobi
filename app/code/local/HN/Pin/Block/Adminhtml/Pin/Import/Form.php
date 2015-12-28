<?php
class HN_Pin_Block_Adminhtml_Pin_Import_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array('id' => 'import_form',
		'action' => $this->getUrl('*/*/import', array('id' => $this->getRequest()->getParam('id'))),
		'method' => 'post','enctype' => 'multipart/form-data'));
		$form->setUseContainer(true);
		$this->setForm($form);
		$fieldset = $form->addFieldset('import_form_set', array('legend'=>Mage::helper('pin')->__('Import csv')));
		
		$fieldset->addField('csv_file', 'file', array(
'label' => Mage::helper('pin')->__('Import csv'),
'class' => 'required-entry',
'type'	=>'file' ,	
'required' => true,
'name' => 'csv_file',
		));
		return parent::_prepareForm();
	}
}
