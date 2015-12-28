<?php

class HN_Pin_Block_Adminhtml_Pin_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected  $_datapin ;
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array('id' => 'addgiftvoucher', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data'));

		$this->_datapin = Mage::getSingleton('adminhtml/session')->getpinData();

		$this->setForm($form);
		$note = "Pattern examples <br/><strong>[A.8] : 8 alpha chars<br>[N.4] : 4 numerics<br>[AN.6] : 6 alphanumeric<br>GIFT-[A.4]-[AN.6] : GIFT-ADFA-12NF0O</strong>";

		$fieldset = $form->addFieldset('pin_form', array('legend'=>Mage::helper('pin')->__('PIN Information')));

		$fieldset->addField('product_name', 'text', array(
		'label' => Mage::helper('pin')->__('Product Name'),
		'class' => 'grey',
		'style' =>'background:grey',
		'readonly' => 'readonly',
		'name' => 'product_name',
		));



		$options = array(
		HN_Pin_Model_Pin::STATUS_AVAILABLE => HN_Pin_Model_Pin::STATUS_AVAILABLE,
		HN_Pin_Model_Pin::STATUS_EXPIRED => HN_Pin_Model_Pin::STATUS_EXPIRED,
		HN_Pin_Model_Pin::STATUS_SOLD_OUT =>HN_Pin_Model_Pin::STATUS_SOLD_OUT
		);

		$fieldset->addField('status', 'select', array(
            'name' => 'status',
            'label' => Mage::helper('catalog')->__('Status'),
            'title' => Mage::helper('catalog')->__('Status'),
            'values' => $options,
		));

		if (isset($this->_datapin['filetype'])) {
			if ($this->_datapin['filetype'] == HN_Pin_Model_Pin::TEXT_TYPE) {
				$decryptPin = Mage::helper('core')->decrypt($this->_datapin['pin_number']);
				
				$fieldset->addField('pin_number', 'text', array(
						'label' => Mage::helper('pin')->__('PIN number'),
						'class' => 'required-entry',
						'name' => 'pin_number',
				));

				$this->_datapin['pin_number'] = $decryptPin;
			}

		}
		$allStores = Mage::app()->getStores();
		$store_option = array();
		$store_name = array();
		$store_name[] = "All store view";
		foreach ($allStores as $_eachStoreId => $val)  {
			$store_option[] = Mage::app()->getStore($_eachStoreId)->getId();
			$store_name[] = Mage::app()->getStore($_eachStoreId)->getName();
		}


		//end of shipping method
		if ( $this->_datapin )
		{
			$form->setValues($this->_datapin);
		} 

			
		return parent::_prepareForm();
	}

	protected function _toHtml() {
		$html = parent::_toHtml();

		if (isset($this->_datapin['id'])) {

			if ($this->_datapin['filetype'] != HN_Pin_Model_Pin::TEXT_TYPE ) {
				$html_plus = "<a href='" . Mage::getUrl('pin/adminhtml_pin/viewfilepin' ,array('id' =>$this->_datapin['id']) )  ."' >". "View File" . "</a>";
				$html =$html.$html_plus;
			}
		}

		return $html;
	}

}

