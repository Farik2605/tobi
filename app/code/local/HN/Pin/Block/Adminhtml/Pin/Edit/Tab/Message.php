<?php

class HN_Giftcert_Block_Adminhtml_Giftcert_Edit_Tab_Message extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		//$form = new Varien_Data_Form(array("encrypt","multipart/form-data"));
        $form = new Varien_Data_Form(array('id' => 'addgiftvoucher', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data'));
		$form->setData('enctype','multipart/form-data');
		$form->setData('id','addgiftvoucher');
		$this->setForm($form);
		$fieldset = $form->addFieldset('customer', array('legend'=>Mage::helper('giftcert')->__('Customer')));
		$fieldset->addField('customer_name', 'text', array(
		'label' => Mage::helper('giftcert')->__('Customer name'),
		
		'name' => 'customer_name',
				));

		$fieldset->addField('customer_email', 'text', array(
		'label' => Mage::helper('giftcert')->__('Customer email'),
		
		'name' => 'customer_email',
				));		       
		$fieldset_recipient = $form->addFieldset('recipient', array('legend'=>Mage::helper('giftcert')->__('Recipient')));
		
		$fieldset_recipient->addField('recipient_name', 'text', array(
'label' => Mage::helper('giftcert')->__('Recipient name'),

'name' => 'recipient_name',
		));
		
		$fieldset_recipient->addField('recipient_email', 'text', array(
'label' => Mage::helper('giftcert')->__('Recipient email'),

'name' => 'recipient_email',
		));
		
		$fieldset_recipient->addField('recipient_address', 'textarea', array(
'label' => Mage::helper('giftcert')->__('Recipient Address'),

'name' => 'recipient_address',
		));
		
$fieldsetmess = $form->addFieldset('message_set', array('legend'=>Mage::helper('giftcert')->__('Message')));

	$fieldsetmess->addField('message', 'textarea', array(
'label' => Mage::helper('giftcert')->__('Message'),

'name' => 'message',
		));
		if ( Mage::getSingleton('adminhtml/session')->getgiftcertData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getgiftcertData());
			Mage::getSingleton('adminhtml/session')->setgiftcertData(null);
		} elseif ( Mage::registry('giftcert_data') ) {
			$form->setValues(Mage::registry('giftcert_data')->getData());
		}
		return parent::_prepareForm();
    }
}
