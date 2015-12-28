<?php
class HN_Pin_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action {
	public function indexAction() {
		$this->loadLayout ()->_setActiveMenu ( 'catalog/pin' );
		// $this->getResponse()->setBody(
		// $this->getLayout()->createBlock('pin/adminhtml_pin_grid')->toHtml());
		
		$this->_addContent ( $this->getLayout ()->createBlock ( 'pin/adminhtml_order' ) );
		$this->renderLayout ();
	}
	public function viewAction() {
		$orderId = $this->getRequest ()->getParam ( 'order_id' );
		if (! $orderId) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'pin' )->__ ( 'Item does not exist' ) );
			$this->_redirect ( '*/*/' );
		}
		
		$collectionText = Mage::getResourceModel ( 'pin/orderpin_collection' )->getTextByOrder ( $orderId );
		;
		Mage::register ( 'texts', $collectionText );
		$collectionFiles = Mage::getResourceModel ( 'pin/orderpin_collection' )->getFileByOrder ( $orderId );
		;
		Mage::register ( 'files', $collectionFiles );
		$this->loadLayout ()->_setActiveMenu ( 'catalog/pin' );
		$this->renderLayout ();
	}
	public function deliverypinAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		if (! $id) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'pin' )->__ ( 'Item does not exist' ) );
			$this->_redirect ( '*/*/' );
		}
		$orderItem = Mage::getModel('sales/order_item')->load($id);
		try {
		$listener = Mage::getSingleton ( 'pin/observer' );
		$listener->deliveryPin ( $orderItem );
		
		Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'pin' )->__ ( 'Delivery PIN sucessfully' ) );
		$this->_redirect ( '*/*/' );
		} catch (Exception $e) {
			
		}
	}
}
