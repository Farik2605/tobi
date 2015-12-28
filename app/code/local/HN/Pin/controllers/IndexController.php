<?php
/**
 * HungnamEcommerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://hungnamecommerce.com/HN-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   HN
 * @package    HN_PIN
 * @version    2.0.3
 * @copyright  Copyright (c) 2012-2013 HungnamEcommerce Co. (http://hungnamecommerce.com)
 * @license    http://hungnamecommerce.com/HN-LICENSE-COMMUNITY.txt
 */


class HN_Pin_IndexController extends Mage_Core_Controller_Front_Action
{

	public function indexAction()
	{
		$template = Mage::getConfig()->getNode('global/page/layouts/'.Mage::getStoreConfig("featuredproducts/general/layout").'/template');

		$this->loadLayout();

		$this->getLayout()->getBlock('root')->setTemplate($template);
		$this->getLayout()->getBlock('head')->setTitle($this->__(Mage::getStoreConfig("featuredproducts/general/meta_title")));
		$this->getLayout()->getBlock('head')->setDescription($this->__(Mage::getStoreConfig("featuredproducts/general/meta_description")));
		$this->getLayout()->getBlock('head')->setKeywords($this->__(Mage::getStoreConfig("featuredproducts/general/meta_keywords")));

		$this->renderLayout();
	}


	public function viewfileAction() {
		$id= $this->getRequest()->getParam('id');
		if ( $id && $this->hasPermission($id) ) {
			$orderPin = Mage::getModel('pin/orderpin')->load($id);
			$file_content = $orderPin->getFileblob();
			$filetype= $orderPin->getFiletype();
			if ($filetype =='') $filetype = 'application/octet-stream';
			return $this->_prepareDownloadResponse($orderPin->getProductName(), $file_content, $filetype);
		}
	}

	private function hasPermission($id) {
		$_customer  = Mage::getSingleton('customer/session')->isLoggedIn() ? Mage::getSingleton('customer/session')->getCustomer() : null;

		if ($_customer == null) {
			return false;
		} else {
			$customerId = $_customer->getId();
			//Mage::getResourceModel('pin/orderpin_collection')->getFilePinPerCs($customerId);
			$orderPinObject = Mage::getResourceModel('pin/orderpin_collection')->havePermission($customerId , $id);
			if (is_object($orderPinObject) && $orderPinObject->getCustomerId() ==$customerId) {
				return true;
			}
		}
		return false;
	}

}