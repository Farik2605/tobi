<?php
/**
@category Hungnam Game delivery license * @package
*Hungnamecommerce solutions * @author Luu Thanh Thuy, luuthuy205
@gmail.com * */
class HN_Pin_Model_Observer {
	public function catalogProductLoadAfter($observer) {
		$action = Mage::app ()->getFrontController ()->getAction ();
		if (is_object ( $action )) {
			if ($action->getFullActionName () == 'checkout_cart_add') {
				// assuming you are posting your custom form values in an array called extra_options...
				if ($options = $action->getRequest ()->getParam ( 'extra_options' )) {
					
					$product = $observer->getProduct ();
					$options = $action->getRequest ()->getParam ( 'extra_options' );
					// $options= array('balance'=>'20');
					// add to the additional options array
					$additionalOptions = array ();
					if ($additionalOption = $product->getCustomOption ( 'additional_options' )) {
						$additionalOptions = ( array ) unserialize ( $additionalOption->getValue () );
					}
					foreach ( $options as $key => $value ) {
						$additionalOptions [] = array (
								'label' => $key,
								'value' => $value 
						);
					}
					// add the additional options array with the option code additional_options
					$observer->getProduct ()->addCustomOption ( 'additional_options', serialize ( $additionalOptions ) );
				}
			}
		}
	}
	/**
	 *
	 * @param object $observer        	
	 */
	public function orderCommitListener($observer) {
		$order = $observer->getEvent ()->getOrder ();
		
		foreach ( $order->getAllItems () as $orderItem ) {
			$this->deliveryPin($orderItem);
		}
		
		return $this;
	}
	
	/**
	 * ription :
	 * 
	 * @version :2.0.3
	 * @author : luuthuy205@gmail.com
	 */
	public function savePinOrderItem($observer) {
		$orderItem = $observer->getEvent ()->getItem ();
		$this->deliveryPin($orderItem);
		return $this;
	}
	
	public function deliveryPin($orderItem) {
		$pinHelper = Mage::helper ( 'pin' );
		
		$order = $orderItem->getOrder ();
		$safe_to_delivery = true;
		$customer_email = $order->getData ( 'customer_email' );
		$customer_firstname = $order->getData ( 'customer_firstname' );
		$customer_lastname = $order->getData ( 'customer_lastname' );
		$text_pinArr = array ();
		$file_pinArr = array ();
		if (! $orderItem->getId ()) {
			// order not saved in the database
			return $this;
		}
		$pin_order_status_bean = Mage::getModel ( 'pin/orderpin_status' );
		/**
		 * Risk management
		*/
		$high_risk_payment_method = Mage::getStoreConfig ( 'pin/risk_management/payment_method' );
		$high_risk_payment_method = explode ( ',', $high_risk_payment_method );
		
		$safe_customer_group = Mage::getStoreConfig ( 'pin/risk_management/customer_group' );
		$safe_customer_group = explode ( ',', $safe_customer_group );
		
		$payment = $order->getPayment ();
		$payment_method = $payment->getMethod ();
		$websiteId = Mage::app ()->getWebsite ()->getId ();
		$customer = Mage::getModel ( 'customer/customer' )->setWebsiteId ( $websiteId )->loadByEmail ( $customer_email );
		$customer_group_id = $customer->getGroupId ();
		
		if (in_array ( $payment_method, $high_risk_payment_method )) {
			if (in_array ( $customer_group_id, $safe_customer_group )) { // safe to delivery keys
				$safe_to_delivery = true;
			} else {
				$safe_to_delivery = false;
			}
		}
		$product = $orderItem->getProduct ();
		
		if ($product && $pinHelper->isPinProduct ( $product->getId () )) {
			// if the product is pin product then continue else do not process it
			$pin_order_status_bean = Mage::getModel ( 'pin/orderpin_status' )->load ( $orderItem->getId(), 'item_id' );
			if ( is_object($pin_order_status_bean) &&$pin_order_status_bean->isComplete()) {
				return $this;
			}
			//first time this code run --> create an record
			if (!$pin_order_status_bean->getId()) {
				Mage::getModel('pin/orderpin_status')->setData(array(
				'order_id' =>$order->getId(),
				'item_id' =>$orderItem->getId(),
				'product_id' =>$product->getId(),
				'product_name' =>$product->getName(),
				'product_sku' =>$product->getSku(),
				'delivery_status'=>0,
				'total_qty' =>$orderItem->getQtyOrdered (),
				'delivery_qty'=>0
				)) ->save();
			}
		
				
				
			if ($orderItem->getStatusId () == Mage_Sales_Model_Order_Item::STATUS_INVOICED && $safe_to_delivery) {
				$is_pin_order_item_exist = false;
				$orderpinModel = Mage::getModel ( 'pin/orderpin' )->load ( $orderItem->getId (), 'order_item_id' );
				if (is_object ( $orderpinModel )) {
					if ($orderpinModel->getData ( 'order_item_id' ) == $orderItem->getId ()) {
						$is_pin_order_item_exist = true;
					}
						
					if (! $is_pin_order_item_exist) {
						$product = $orderItem->getProduct ();
						$productId = $product->getId ();
						/* is product has pin */
						$isPinProduct = $pinHelper->isPinProduct ( $productId );
		
						$qty = $orderItem->getQtyOrdered ();
						if ($isPinProduct) {
								
							$order_id = $order->getData ( 'entity_id' ); // $order->getData('entity_id');
							$customer_id = $order->getData ( 'customer_id' );
							$order_increment_id = $order->getIncrementId ();
								
							$productPinInfo = Mage::getResourceModel ( 'pin/ispinproduct' )->getPinTypeByProductId ( $productId );
								
							/**
							 * type ==1 is text, type ==2 is file
							*/
							if ($productPinInfo [0] == 1) {
								/**
								 * get available pin
								 */
								$dataTxt = Mage::getResourceModel ( 'pin/pin_collection' )->getActivePinTextByProductQty ( $productId, $qty );
		
								if (! empty ( $dataTxt )) {
										
									$order_item_id = $orderItem->getId ();
									$product_name = $product->getName ();
									$product_sku = $product->getSku ();
										
									/**
									 * save in the database
									*/
									foreach ( $dataTxt as $pinRecord ) {
										$pin_number = $pinRecord ['pin_number'];
										$filetype = $pinRecord ['filetype'];
										$oderpinModel = Mage::getModel ( 'pin/orderpin' );
										$oderpinData = array (
												'order_id' => $order_id,
												'order_increment_id' => $order_increment_id,
												'order_item_id' => $order_item_id,
												'customer_id' => $customer_id,
												'product_name' => $product_name,
												'product_sku' => $product_sku,
												'pin_number' => $pin_number,
												'filetype' => $filetype
										);
		
										$oderpinModel->setData ( $oderpinData );
		
										$oderpinModel->save ();
										Mage::getModel ( 'pin/pin' )->load ( $pinRecord ['id'] )->setStatus ( HN_Pin_Model_Pin::STATUS_SOLD_OUT )->save ();
										$text_pinArr [] = array (
												'name' => $product_name,
												'pin' => Mage::helper ( 'core' )->decrypt ( $pin_number )
										);
									}
									$pin_order_status_bean->setData('delivery_qty', count($dataTxt));
									if (count($dataTxt) ==$pin_order_status_bean->getData('total_qty')) {
										$pin_order_status_bean->setData('delivery_status', 1);
		
									}
									$pin_order_status_bean->save();
								}
							} 							// end of if pin is text
							elseif ($productPinInfo [0] == 2) {
		
								/**
								 * get available file pin
								 */
								$dataFile = Mage::getResourceModel ( 'pin/pin_collection' )->getActivePinFileByProductQty ( $productId, $qty );
		
								if (! empty ( $dataFile )) {
										
									$order_item_id = $orderItem->getId ();
									$product_name = $product->getName ();
									$product_sku = $product->getSku ();
										
									/**
									 * save in the database
									*/
									foreach ( $dataFile as $pinRecord ) {
		
										$fileblob = $pinRecord ['fileblob'];
										$filetype = $pinRecord ['filetype'];
										$oderpinModel = Mage::getModel ( 'pin/orderpin' );
										$oderpinData = array (
												'order_id' => $order_id,
												'order_increment_id' => $order_increment_id,
												'order_item_id' => $order_item_id,
												'customer_id' => $customer_id,
												'product_name' => $product_name,
												'product_sku' => $product_sku,
												'fileblob' => $fileblob,
												'filetype' => $filetype
										);
		
										$oderpinModel->setData ( $oderpinData );
										$oderpinModel->save ();
		
										Mage::getModel ( 'pin/pin' )->load ( $pinRecord ['id'] )->setStatus ( HN_Pin_Model_Pin::STATUS_SOLD_OUT )->save ();
										$file_pinArr [] = array (
												'name' => $product_name . "." . $pinRecord ['ext'],
												'pin' => $fileblob
										);
									}
										
									$pin_order_status_bean->setData('delivery_qty', count($dataFile));
									if (count($dataFile) ==$pin_order_status_bean->getData('total_qty')) {
										$pin_order_status_bean->setData('delivery_status', 1);
											
									}
									$pin_order_status_bean->save();
								}
							} // end of if pin is file
						}
					}
						
					try {
						if (! isset ( $text_pinArr ))
							$text_pinArr = array ();
						if (! isset ( $file_pinArr ))
							$file_pinArr = array ();
						Mage::log ( $text_pinArr, null, 'pindebug.log', true );
						Mage::log ( $file_pinArr, null, 'pindebug.log', true );
						if (! empty ( $text_pinArr ) || ! empty ( $file_pinArr ))
							Mage::helper ( 'pin' )->sendEmailToBuyer ( $customer_email, $customer_firstname, $customer_lastname, $text_pinArr, $file_pinArr );
					} catch ( Exception $e ) {
						Mage::logException ( $e );
					}
				}
			}
		}
	}
	/**
	 * Set status of link
	 *
	 * @param Varien_Object $observer        	
	 * @return Mage_Downloadable_Model_Observer
	 */
	public function setPinStatus($observer) {
		$order = $observer->getEvent ()->getOrder ();
		
		if (! $order->getId ()) {
			// order not saved in the database
			return $this;
		}
		
		/* @var $order Mage_Sales_Model_Order */
		$status = '';
		$pinStatuses = array (
				'pending' => HN_Pin_Model_Pin::STATUS_PENDING,
				'expired' => HN_Pin_Model_Pin::STATUS_EXPIRED,
				'avail' => HN_Pin_Model_Pin::STATUS_AVAILABLE,
				'payment_pending' => HN_Pin_Model_Pin::STATUS_PENDING_PAYMENT,
				'payment_review' => HN_Pin_Model_Pin::STATUS_PAYMENT_REVIEW,
				'sold_out' => HN_Pin_Model_Pin::STATUS_SOLD_OUT 
		);
		
		$pinItemsStatuses = array ();
		
		if ($order->getState () == Mage_Sales_Model_Order::STATE_COMPLETE) {
			
			{
				foreach ( $order->getAllItems () as $item ) {
					if ($item->getProductType () == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE || $item->getRealProductType () == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
						if (in_array ( $item->getStatusId (), $expiredStatuses )) {
							$downloadableItemsStatuses [$item->getId ()] = $linkStatuses ['expired'];
						} else {
							$downloadableItemsStatuses [$item->getId ()] = $linkStatuses ['avail'];
						}
					}
				}
			}
			
			return $this;
		}
	}
	
	/**
	 * send mail to notification low stock
	 */
	public function scheduledSend() {
		$storeId = Mage::app ()->getStore ()->getId ();
		$pin_is_notification = Mage::getStoreConfig ( 'pin/general/low_stock_notification', $storeId );
		$pin_number_notification = Mage::getStoreConfig ( 'pin/general/low_stock_number', $storeId );
		$info = array ();
		
		$availPinProductCollection = Mage::getResourceModel ( 'pin/ispinproduct_collection' )->getProductPIN ();
		/**
		 */
		foreach ( $availPinProductCollection as $item ) {
			
			echo $item->getData ( 'type' );
			if ($item->getType () == 1) {
				$pinCollection = Mage::getResourceModel ( 'pin/pin_collection' )->getActivePinTextByProduct ( $item->getProductId () );
			} elseif ($item->getType () == 2) {
				$pinCollection = Mage::getResourceModel ( 'pin/pin_collection' )->getActivePinFileByProduct ( $item->getProductId () );
			}
			$number_pin = $pinCollection->getSize ();
			
			if ($number_pin < $pin_number_notification) {
				$info [] = array (
						'product_id' => $item->getProductId (),
						'qty' => $number_pin 
				);
			}
		}
	/**
	 */
	}
	
	/**
	 * fix manholau website
	 */
	public function scheduleCheckOrder() {
		
		// $ordercol = Mage::getResourceModel('sales/order_collection');
		// $ordercol->getSelect()->where('customer_id=?', $customerId);
		
		// $hour = 12;
		
		// $today = strtotime("$hour:00:00");
		// $yesterday = strtotime('-1 day', $today);
		// echo date("Y-m-d H:i:s\n", $yesterday);
		// $t= date("Y-m-d H:i:s\n", $today);
		$tPrefix = ( string ) Mage::getConfig ()->getTablePrefix ();
		$saleOrderTbl = $tPrefix . "sales_flat_order";
		
		$db = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
		
		$query = "SELECT * FROM $saleOrderTbl WHERE DATE(`created_at`) < CURDATE() - INTERVAL 1 DAY;";
		
		Mage::log ( $query, null, 'hehe.txt', true );
		
		$rs = $db->query ( $query );
		$rows = $rs->fetchAll ( PDO::FETCH_ASSOC );
		
		if (! empty ( $rows )) {
			foreach ( $rows as $orderData ) {
				$this->updateDB ( $orderData ['entity_id'] );
			}
		}
	}
	
	/**
	 *
	 * @param int $id
	 *        	entity_id of the order
	 */
	function updateDB($id) {
		$order = Mage::getModel ( 'sales/order' )->load ( $id );
		
		if (! $order) {
			return $this;
		}
		
		$pinHelper = Mage::helper ( 'pin' );
		$order_id = $order->getData ( 'entity_id' );
		// $send_mail_on_complete = Mage::getStoreConfig('pin/general/change_status');
		
		/**
		 * FOR SENDING EMAIL
		 */
		$customer_email = $order->getData ( 'customer_email' );
		$customer_firstname = $order->getData ( 'customer_firstname' );
		$customer_lastname = $order->getData ( 'customer_lastname' );
		$text_pinArr = array ();
		$file_pinArr = array ();
		
		/**
		 */
		if ($order->getState () == Mage_Sales_Model_Order::STATE_HOLDED) {
			$status = 'pending';
			return;
		} elseif ($order->isCanceled () || $order->getState () == Mage_Sales_Model_Order::STATE_CLOSED || $order->getState () == Mage_Sales_Model_Order::STATE_COMPLETE) {
			$expiredStatuses = array (
					Mage_Sales_Model_Order_Item::STATUS_CANCELED,
					Mage_Sales_Model_Order_Item::STATUS_REFUNDED 
			);
		}
		/**
		 * loop through all items in order
		 */
		
		foreach ( $order->getAllItems () as $item ) {
			Mage::log ( 'updateDB', null, 'paypald.log', true );
			Mage::log ( $item->getStatusId (), null, 'paypald.log', true );
			if ($item->getStatusId () == Mage_Sales_Model_Order_Item::STATUS_INVOICED) {
				$is_pin_order_item_exist = false;
				$orderpinModel = Mage::getModel ( 'pin/orderpin' )->load ( $item->getId (), 'order_item_id' );
				if (is_object ( $orderpinModel )) {
					if ($orderpinModel->getData ( 'order_item_id' ) == $item->getId ()) {
						$is_pin_order_item_exist = true;
					}
				}
				
				if (! $is_pin_order_item_exist) {
					$product = $item->getProduct ();
					$productId = $product->getId ();
					/* is product has pin */
					$isPinProduct = $pinHelper->isPinProduct ( $productId );
					
					$qty = $item->getQtyOrdered ();
					if ($isPinProduct) {
						
						$order_id = $order->getData ( 'entity_id' ); // $order->getData('entity_id');
						$customer_id = $order->getData ( 'customer_id' );
						$order_increment_id = $order->getIncrementId ();
						
						$productPinInfo = Mage::getResourceModel ( 'pin/ispinproduct' )->getPinTypeByProductId ( $productId );
						
						/**
						 * type ==1 is text, type ==2 is file
						 */
						if ($productPinInfo [0] ['type'] == 1) {
							/**
							 * get available pin
							 */
							$dataTxt = Mage::getResourceModel ( 'pin/pin_collection' )->getActivePinTextByProductQty ( $productId, $qty );
							
							if (! empty ( $dataTxt )) {
								
								$order_item_id = $item->getId ();
								$product_name = $product->getName ();
								$product_sku = $product->getSku ();
								
								/**
								 * save in the database
								 */
								foreach ( $dataTxt as $pinRecord ) {
									$pin_number = $pinRecord ['pin_number'];
									$filetype = $pinRecord ['filetype'];
									$oderpinModel = Mage::getModel ( 'pin/orderpin' );
									$oderpinData = array (
											'order_id' => $order_id,
											'order_increment_id' => $order_increment_id,
											'order_item_id' => $order_item_id,
											'customer_id' => $customer_id,
											'product_name' => $product_name,
											'product_sku' => $product_sku,
											'pin_number' => $pin_number,
											'filetype' => $filetype 
									);
									
									$oderpinModel->setData ( $oderpinData );
									
									$oderpinModel->save ();
									Mage::getModel ( 'pin/pin' )->load ( $pinRecord ['id'] )->setStatus ( HN_Pin_Model_Pin::STATUS_SOLD_OUT )->save ();
									$text_pinArr [] = array (
											'name' => $product_name,
											'pin' => Mage::helper ( 'core' )->decrypt ( $pin_number ) 
									);
								}
							}
						} 						// end of if pin is text
						elseif ($productPinInfo [0] ['type'] == 2) {
							
							/**
							 * get available file pin
							 */
							$dataFile = Mage::getResourceModel ( 'pin/pin_collection' )->getActivePinFileByProductQty ( $productId, $qty );
							
							if (! empty ( $dataFile )) {
								
								$order_item_id = $item->getId ();
								$product_name = $product->getName ();
								$product_sku = $product->getSku ();
								
								/**
								 * save in the database
								 */
								foreach ( $dataFile as $pinRecord ) {
									
									$fileblob = $pinRecord ['fileblob'];
									$filetype = $pinRecord ['filetype'];
									$oderpinModel = Mage::getModel ( 'pin/orderpin' );
									$oderpinData = array (
											'order_id' => $order_id,
											'order_increment_id' => $order_increment_id,
											'order_item_id' => $order_item_id,
											'customer_id' => $customer_id,
											'product_name' => $product_name,
											'product_sku' => $product_sku,
											'fileblob' => $fileblob,
											'filetype' => $filetype 
									);
									
									$oderpinModel->setData ( $oderpinData );
									$oderpinModel->save ();
									
									Mage::getModel ( 'pin/pin' )->load ( $pinRecord ['id'] )->setStatus ( HN_Pin_Model_Pin::STATUS_SOLD_OUT )->save ();
									$file_pinArr [] = array (
											'name' => $product_name . "." . $pinRecord ['ext'],
											'pin' => $fileblob 
									);
								}
							}
						} // end of if pin is file
					}
				}
			}
		}
		try {
			Mage::log ( $text_pinArr, null, 'pindebug.log', true );
			Mage::log ( $file_pinArr, null, 'pindebug.log', true );
			if (! empty ( $text_pinArr ) || ! empty ( $file_pinArr ))
				Mage::helper ( 'pin' )->sendEmailToBuyer ( $customer_email, $customer_firstname, $customer_lastname, $text_pinArr, $file_pinArr );
		} catch ( Exception $e ) {
			Mage::logException ( $e );
		}
	}
	public function orderInvoicePayListener($observer) {
		$invoice = $observer->getEvent ()->getInvoice ();
		Mage::log ( $invoice->getState (), null, 'paypald.log', true );
		switch ($invoice->getState ()) {
			case Mage_Sales_Model_Order_Invoice::STATE_PAID :
				$orderId = $invoice->getOrderId ();
				$this->updateDB ( $orderId );
				break;
		}
		return $this;
	}
	
	/**
	 * event after a customer group change
	 */
	public function customerGroupChangeListener($observer) {
		Mage::log ( '======go in customerGroupChangeListener', null, 'sp.log', true );
		try {
			$customer = $observer->getCustomer ();
			
			$orderCollection = Mage::getModel ( 'sales/order' )->getCollection ()->addFieldToFilter ( 'customer_id', array (
					'eq' => array (
							$customer->getId () 
					) 
			) );
			if (count ( $orderCollection ) > 0) {
				
				foreach ( $orderCollection as $order ) {
					foreach ( $order->getAllItems () as $item ) {
						$event = new Varien_Object ();
						$event->setItem ( $item );
						$arg_observer = new Varien_Object ();
						$arg_observer->setEvent ( $event );
						$this->savePinOrderItem ( $arg_observer );
					}
				}
			}
		} catch ( Exception $e ) {
		}
		Mage::log ( '======go out customerGroupChangeListener', null, 'sp.log', true );
	}
}
