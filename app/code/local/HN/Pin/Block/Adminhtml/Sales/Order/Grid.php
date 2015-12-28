<?php
class HN_Pin_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{

	public function __construct()
	{
		parent::__construct();
	}

	protected function _prepareMassaction()
	{
		parent::_prepareMassaction();
		
		$this->getMassactionBlock()->addItem('seperator1', array(
		     'label'=> Mage::helper('sales')->__('---------------'),
		     'url'  => '',
		));
		
	
		$this->getMassactionBlock()->addItem('sendpin_order', array(
		     'label'=> Mage::helper('sales')->__('Send PIN'),
		     'url'  => $this->getUrl('pin/adminhtml_pin/massSendPin'),
			));
		
		return $this;
	}
}
