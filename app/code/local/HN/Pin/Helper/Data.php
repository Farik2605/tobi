<?php
/**
 *
 * @category   Hungnam Game delivery license
 * @package    Hungnamecommerce solutions
 * @author     Luu Thanh Thuy, <luuthuy205@gmail.com>
 */
class HN_Pin_Helper_Data extends Mage_Core_Helper_Abstract

{
	/**
	 *
	 * @param int $productId
	 * @return int $pin_qty;
	 */
	public function getQtyPINAvail($productId) {

		$productPinInfo = Mage::getResourceModel('pin/ispinproduct')->getPinTypeByProductId($productId);
		if (!empty($productPinInfo)) {
			/** type ==1 is text, type ==2 is file */
			if ($productPinInfo[0]== 1) {
				$dataTxt =  Mage::getResourceModel('pin/pin_collection')->getQtyAvailTextPin();
				$pin_qty  = count($dataTxt);

			} elseif ($productPinInfo[0] == 2)  {
				$filePIN =  Mage::getResourceModel('pin/pin_collection')->getQtyAvailFilePin();
				$pin_qty  = count($filePIN);
			}
				
			return $pin_qty;
		}
	}
	/**
	 * get Active PIN
	 */
	public function getActivePin($productId) {
		return Mage::getResourceModel('pin/pin_collection')->getActivePin($productId);
	}
	/**
	 * get pininfo per order
	 */
	public function getPinInfo( $entity_id) {
		$collectionData = Mage::getResourceModel('pin/orderpin_collection')->getPinByOrder($entity_id);
		return $collectionData;
	}

	public function updateOrderpinnumber($order_id, $number,$customer_id) {

		$model = Mage::getModel('pin/orderpin');
		$model->setData('order_id',$order_id );
		$model->setData('pin_number',$number );
		$model->setData('customer_id',$customer_id );
		$model->save();
	}
	public function updateOrderpinfile($order_id,  $file,$customer_id) {

		$model = Mage::getModel('pin/orderpin');
		$model->setData('order_id',$order_id );
		$model->setData('file',$file );
		$model->setData('customer_id',$customer_id );
		$model->save();
	}

	/**
	 * whether product is pin product or not
	 * @param int $productId
	 */
	public function isPinProduct($productId) {
		$product = Mage::getModel('pin/ispinproduct')->load($productId, 'product_id');
		if (is_object($product)) {
			if ($product->getStatus() == 1) return true;
		}
		return false;
	}


	/**
	 * @description : send email to customer to notify about the PIN
	 */
	public function sendEmailToBuyer($customer_email, $customer_firstname, $customer_lastname , $text_pinArr, $file_pinArr) {
		if ($customer_email) {
			$html = "<table class='pin_table'> <tr> <td>" . Mage::helper('pin')->__('Product name') . "</td> <td> ".
		 Mage::helper('pin')->__('PIN ')
		 ."</td> </tr>";
		 if ( !empty($text_pinArr)) {
		 	foreach ($text_pinArr as $pin) {

		 		$html .="<tr> <td>". $pin['name']. "</td> <td>". $pin['pin']. "</td> </tr>";

		 	}
		 }
		 $html .="</table>";

		 Mage::log($html, null, 'pin.log' , true);
		 /**
		  *
		  * @var unknown_type
		  */

		 $translate = Mage::getSingleton('core/translate');
		 /* @var $translate Mage_Core_Model_Translate */
		 $translate->setTranslateInline(false);

		 $mailTemplate = Mage::getModel('core/email_template');
		 /* @var $mailTemplate Mage_Core_Model_Email_Template */
		 $template = "pin_information_template";
		 $sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
		 $sender_name = Mage::getStoreConfig('trans_email/ident_general/name');
		 $sender = array('email'=>$sender_email,'name'=>$sender_name);
		 $recipient['email'] = $customer_email;
		 $recipient['name']= $customer_firstname. " " .  $customer_lastname;
		 ////////////Attach //////////////
		 if ( !empty($file_pinArr)) {
		 	foreach ($file_pinArr as $file) {

		 		$filename = $file['name'];
		 		$fileContents = $file['pin'];
		 		$attachment = $mailTemplate->getMail()->createAttachment($fileContents);
		 		
		 		$attachment->filename = $filename;

		 	}
		 }
		 ////////////End of Attach///////////////
		 /////////////////////////////////

		 $mailTemplate->setTemplateSubject($this->__('Game license serial key'));
		 $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>Mage::app()->getStore()->getId()))
		 ->sendTransactional(
		 $template,
		 $sender,
		 $recipient['email'],
		 $recipient['name'],
		 array(
          'customer' =>$customer_firstname. " " .  $customer_lastname,
          'textmail' => $html     
		 )
		 );

		 $translate->setTranslateInline(false);
		 return $this;

		}
	}

	/**
	 *
	 * @param array $info = array('product_id' =>$item->getProductId() , 'qty' =>$number_pin);
	 */
	public function sendLowStockNotification($info) {

		$html = '';
		if (!empty($info)) {
			foreach ($info as $index=>$data) {
				$product = Mage::getModel('catalog/product')->load($data['product_id']);

				$html .= $index . ". ". $product->getName() . "   sku ". $product->getSku(). '  have quatity '. $data['qty'] . "<br>"; 
					
				/**
				 *
				 * @var unknown_type
				 */

				$translate = Mage::getSingleton('core/translate');
				/* @var $translate Mage_Core_Model_Translate */
				$translate->setTranslateInline(false);

				$mailTemplate = Mage::getModel('core/email_template');
				/* @var $mailTemplate Mage_Core_Model_Email_Template */
				$template = "pin_information_template";
				$sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
				$sender_name = Mage::getStoreConfig('trans_email/ident_general/name');
				$sender = array('email'=>$sender_email,'name'=>$sender_name);
					
				/** */
				$storeId = Mage::app()->getStore()->getId();
					
				$pin_receiver1 = Mage::getStoreConfig('pin/general/low_stock_email_receiver1' ,$storeId);
				$pin_receiver1name = Mage::getStoreConfig('pin/general/low_stock_email_receiver1name' ,$storeId);
					
				$pin_receiver2 = Mage::getStoreConfig('pin/general/low_stock_email_receiver2' ,$storeId);
				$pin_receiver2name = Mage::getStoreConfig('pin/general/low_stock_email_receiver2name' ,$storeId);
					
				$pin_receiver3 = Mage::getStoreConfig('pin/general/low_stock_email_receiver3' ,$storeId);
				$pin_receiver3name = Mage::getStoreConfig('pin/general/low_stock_email_receiver3name' ,$storeId);
					
				$recipients = array(
				$pin_receiver1 => $pin_receiver1name,
				$pin_receiver2 => $pin_receiver2name,
				$pin_receiver3=> $pin_receiver3name
				);
				 
				 
				$recipient['email'] = $customer_email;
				$recipient['name']= $customer_firstname. " " .  $customer_lastname;
				////////////Attach //////////////
				if ( !empty($file_pinArr)) {
					foreach ($file_pinArr as $file) {

						$filename = $file['name'];
						$fileContents = $file['pin'];
						$attachment = $mailTemplate->getMail()->createAttachment($fileContents);
						$product = Mage::getModel('catalog/product')->load();
						$attachment->filename = $filename;

					}
				}
				////////////End of Attach///////////////
				/////////////////////////////////

				$mailTemplate->setTemplateSubject($this->__('Notification low stock PIN'));
				$mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>Mage::app()->getStore()->getId()))
				->sendTransactional(
				$template,
				$sender,
				array_keys($recipient),
				array_values($recipient),
				array(
                'textmail' => $html     
				)
				);

				$translate->setTranslateInline(false);
				return $this;
			}
		}
	}

}
