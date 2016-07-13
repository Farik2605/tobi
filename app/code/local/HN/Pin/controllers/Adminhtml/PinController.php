<?php
/**
 * HungnamEcommerce Co.
 * @category   HN
 * @package    HN_PIN
 * @version    2.0.3
 * @copyright  Copyright (c) 2012-2013 HungnamEcommerce Co. (http://hungnamecommerce.com)
 * @license    http://hungnamecommerce.com/HN-LICENSE-COMMUNITY.txt
 */
class HN_Pin_Adminhtml_PinController extends Mage_Adminhtml_Controller_Action {
	protected function _initProduct() {
		$product = Mage::getModel ( 'catalog/product' )->setStoreId ( $this->getRequest ()->getParam ( 'store', 0 ) );
		
		if ($setId = ( int ) $this->getRequest ()->getParam ( 'set' )) {
			$product->setAttributeSetId ( $setId );
		}
		
		if ($typeId = $this->getRequest ()->getParam ( 'type' )) {
			$product->setTypeId ( $typeId );
		}
		
		$product->setData ( '_edit_mode', true );
		
		Mage::register ( 'product', $product );
		
		return $product;
	}
	public function indexAction() {
		$this->_initProduct ();
		
		$this->loadLayout ()->_setActiveMenu ( 'catalog/pin' );
		// $this->getResponse()->setBody(
		// $this->getLayout()->createBlock('pin/adminhtml_pin_grid')->toHtml());
		
		$this->_addContent ( $this->getLayout ()->createBlock ( 'pin/adminhtml_pin' ) );
		$this->renderLayout ();
	}
	public function gridAction() {
		$this->getResponse ()->setBody ( $this->getLayout ()->createBlock ( 'pin/adminhtml_edit_grid' )->toHtml () );
	}
	public function saveAction() {
		$params = $this->getRequest ()->getParams ();
		Mage::log ( $params, null, 'games.log', true );
		
		if (isset ( $params ['id'] )) {
			$model = Mage::getModel ( 'pin/pin' );
			$data = array ();
			$data ['status'] = $params ['status'];
			
			if (isset ( $params ['pin_number'] )) {
				$pin_number = Mage::helper ( 'core' )->encrypt ( $params ['pin_number'] );
				$data ['pin_number'] = $pin_number;
			}
			$model->setData ( $data )->setId ( $params ['id'] )->save ();
		}
	}
	protected function _validateSecretKey() {
		return true;
	}
	public function editAction() {
		$pinid = $this->getRequest ()->getParam ( 'id' );
		if ($pinid) {
			
			$model = Mage::getModel ( 'pin/pin' )->load ( $pinid );
			Mage::getSingleton ( 'adminhtml/session' )->setpinData ( $model->getData () );
			
			$this->loadLayout ();
			$this->getLayout ()->getBlock ( 'head' )->setCanLoadExtJs ( true );
			
			$this->_addContent ( $this->getLayout ()->createBlock ( 'pin/adminhtml_pin_edit' ) )->_addLeft ( $this->getLayout ()->createBlock ( 'pin/adminhtml_pin_edit_tabs' ) );
			
			$this->renderLayout ();
		} else {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'bridalregistry' )->__ ( 'Item does not exist' ) );
			$this->_redirect ( '*/*/' );
		}
		// $tPrefix = (string) Mage::getConfig()->getTablePrefix();
		// $pinTbl = $tPrefix. "pin";
		// $pinvalue = $this->getRequest()->getParam('pin_number');
		// $query = 'UPDATE `pin` SET `pin_number` = "'.$pinvalue.'" WHERE `id` = '.$pinid;
		//
		// $db = Mage::getSingleton('core/resource')->getConnection('core_write');
		//
		// $rs = $db->query($query);
		//
		// $lik = Mage::getBaseUrl().'admin';
	}
	public function deleteAction() {
		$pinid = $this->getRequest ()->getParam ( 'id' );
		if (isset ( $pinid )) {
			$pinObject = Mage::getModel ( 'pin/pin' )->load ( $pinid );
			$pinObject->delete ();
			Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Pin were successfully deleted' ) );
		}
		
		$link = Mage::getUrl ( 'pin/adminhtml_pin/pin' );
		$this->getResponse ()->setRedirect ( $link );
	}
	
	/**
	 * mass delete
	 * 
	 * @author luuthuy205@gmail.com
	 */
	public function massDeleteAction() {
		$pinIds = $this->getRequest ()->getParam ( 'id' );
		if (! is_array ( $pinIds )) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select item(s)' ) );
		} else {
			try {
                $productIds = array();
				foreach ( $pinIds as $id ) {
					$pin_model = Mage::getModel ( 'pin/pin' )->load ( $id );
                    if(!in_array($pin_model->getProductId(),$productIds))
                        $productIds[] = $pin_model->getProductId();
					$pin_model->delete ();
				}
                foreach ($productIds as $id) {
                    Mage::helper("kontentaCw")->synchProductIdQty($id);
                }
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were successfully deleted', count ( $pinIds ) ) );
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
			}
		}
		$this->_redirect ( '*/*/index' );
	}
	
	/**
	 * mass change status
	 */
	public function changeStatusAction() {
		$pinIds = ( array ) $this->getRequest ()->getParam ( 'id' );
		
		$status = $this->getRequest ()->getParam ( 'status' );
		if (empty ( $pinIds ) || $status == null) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select item(s)' ) );
			$this->_redirect ( '*/*/index' );
			return;
		}
		try {
            $productIds = array();
			foreach ( $pinIds as $id ) {
				$pin_model = Mage::getModel ( 'pin/pin' )->load ( $id );
                if(!in_array($pin_model->getProductId(),$productIds))
                    $productIds[] = $pin_model->getProductId();
				$pin_model->setStatus ( $status );
				$pin_model->save ();
			}
            foreach ($productIds as $id) {
                Mage::helper("kontentaCw")->synchProductIdQty($id);
            }

            Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were successfully updated', count ( $pinIds ) ) );
		} catch ( Exception $e ) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
		}
		$this->_redirect ( '*/*/index' );
	}
	/**
	 * import csv function
	 * render the form for importing csv file
	 */
	public function importcsvtextAction() {
		$this->loadLayout ();
		
		$this->_addContent ( $this->getLayout ()->createBlock ( 'pin/adminhtml_pin_import' ) )->_addContent ( $this->getLayout ()->createBlock ( 'pin/adminhtml_pin_import_form' ) );
		
		$this->renderLayout ();
	}
	
	/**
	 * import the file
	 */
	public function importAction() {
		// $this->_init();
		$message = '';
		$post = $this->getRequest ()->getPost ();
		$files = $_FILES;
		if (! isset ( $_FILES ['csv_file'] )) {
			echo "no csv file upload";
		}
		echo $_FILES ['csv_file'] ['error'];
		
		if ($_FILES ['csv_file'] ['error'] == UPLOAD_ERR_OK) {
			// The network file is present and we have to
			// crack it into the records.
			
			$fileHandle = fopen ( $files ['csv_file'] ['tmp_name'], "r" );
			$CSVRecords = array ();
			
			while ( ($csv = fgetcsv ( $fileHandle, 1000, ',' )) != false ) {
				// Iterate the loop while we can't reach the end.
				$CSVRecords [] = $csv;
			}
			
			// Truncate tables.
			// $this->controllerModel->emptyAllTables();
			// pin_number product_id product_name fileblob filetype status
			// Now insert data in the tables and reset tables.
			foreach ( $CSVRecords as $line => $record ) {
				
				if (! isset ( $record [0] ) || ! isset ( $record [1] ) || ! isset ( $record [2] )) {
					$message .= Mage::helper ( 'pin' )->__ ( 'line %d has invalid data  <br>', $line );
					continue;
				}
				
				$pin_number = Mage::helper ( 'core' )->encrypt ( $record [0] );
				$product_sku = $record [1];
				$status = $record [2];
				
				$product_id = ( int ) Mage::getModel ( 'catalog/product' )->getIdBySku ( $product_sku );
				
				if (! is_integer ( $product_id )) {
					$message .= Mage::helper ( 'pin' )->__ ( 'line %d has invalid sku <br>', $line );
					continue;
				}
				
				$product = Mage::getModel ( 'catalog/product' )->load ( $product_id );
				
				$product_name = $product->getName ();
				
				$model = Mage::getModel ( 'pin/pin' );
				
				$insertData = array ();
				
				$insertData ['product_id'] = $product_id;
				$insertData ['product_name'] = $product_name;
				$insertData ['pin_number'] = $pin_number;
				$insertData ['status'] = $status;
				$insertData ['filetype'] = HN_Pin_Model_Pin::TEXT_TYPE;
				
				$model->setData ( $insertData );
				$model->save ();
			}
		}
		
		if ($message != '') {
			
			Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( $message ) );
		}
		
		$this->_redirect ( '*/*/index' );
	}
	
	/**
	 * Export to csv file
	 */
	public function exportCsvAction() {
		$fileName = 'pin.csv';
		$content = $this->getLayout ()->createBlock ( 'pin/adminhtml_pin_grid' )->getCsv ();
		
		$this->_sendUploadResponse ( $fileName, $content );
	}
	protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
		$response = $this->getResponse ();
		$response->setHeader ( 'HTTP/1.1 200 OK', '' );
		$response->setHeader ( 'Pragma', 'public', true );
		$response->setHeader ( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true );
		$response->setHeader ( 'Content-Disposition', 'attachment; filename=' . $fileName );
		$response->setHeader ( 'Last-Modified', date ( 'r' ) );
		$response->setHeader ( 'Accept-Ranges', 'bytes' );
		$response->setHeader ( 'Content-Length', strlen ( $content ) );
		$response->setHeader ( 'Content-type', $contentType );
		$response->setBody ( $content );
		$response->sendResponse ();
		die ();
	}
	/**
	 * iframe in product page to add image
	 */
	/**
	 * Export to csv file
	 */
	public function productAction() {
		$this->loadLayout ();
		$this->renderLayout ();
	}
	
	/**
	 */
	public function uploadzipAction() {
		$this->loadLayout ();
		$this->renderLayout ();
	}
	public function savezipAction() {
		$data = $this->getRequest ()->getParams ();
		if (isset ( $data ['productid'] )) {
			if ((isset ( $data ['ispinproduct'] )) && $data ['ispinproduct'] == 'on') {
				$is_pin_product = 1;
			} else {
				$is_pin_product = 0;
			}
			
			// /////////////////
			if (isset ( $_FILES ['imgzip'] ['name'] ) && $_FILES ['imgzip'] ['name'] != '') {
				/**
				 * upload file to a folder
				 */
				try {
					// var_dump($_FILES['imgzip']);
					$uploader = new Varien_File_Uploader ( $_FILES ['imgzip'] );
					$uploader->setAllowedExtensions ( array (
							'zip' 
					) );
					$uploader->setAllowRenameFiles ( false );
					$uploader->setFilesDispersion ( false );
					$varDir = Mage::getBaseDir ( 'var' );
					$timeOfImport = date ( 'jmY_his' );
					$importReadyDir = $varDir . DS . 'import_zip' . DS . $timeOfImport;
					
					// $uploader->mkdir($importReadyDir);
					// $path = Mage::getBaseDir('media') . DS . 'logo' . DS;
					$zipName = $_FILES ['imgzip'] ['name'];
					$uploader->save ( $importReadyDir, $zipName );
					$fileName = $importReadyDir . DS . $zipName;
					/**
					 */
					$zip = new ZipArchive ();
                    if ($zip->open ( $fileName ) === TRUE){
                        $fileType = pathinfo ($fileName, PATHINFO_EXTENSION );
                        $pinModel = Mage::getModel ( 'pin/pin' );
                        $fp = fopen ( $fileName, 'r' );
                        $content = fread ( $fp, filesize ( $fileName) );
                        $pinModel->setData ( 'fileblob', $content );
                        $pinModel->setData ( 'filetype', $fileType );
                        $pinModel->setData ( 'file', $zipName );
                        $pinModel->setData ( 'status', HN_Pin_Model_Pin::STATUS_AVAILABLE );
                        if (isset ( $data ['productid'] )) {
                            $productModel = Mage::getModel ( 'catalog/product' )->load ( $data ['productid'] );
                            $pinModel->setData ( 'product_id', $data ['productid'] );
                            //$pinModel->setData ( 'invoice_id', $data ['pin_invoice_id'] );
                            $pinModel->setData ( 'product_name', $productName = $productModel->getName () );
                        }

                        $pinModel->save ();
                    }
					//if ($zip->open ( $fileName ) === TRUE) {
					if (false) {
						$zip->extractTo ( $importReadyDir );
						$zip->close ();
						
						/**
						 */
						// Open a known directory, and proceed to read its contents
						$fileNameWithoutZip = substr ( $fileName, 0, strlen ( $fileName ) - 4 );
						
						// delete the zip file
						if (is_file ( $fileName ))
							unlink ( $fileName );
						//if (is_dir ( $fileNameWithoutZip )) {
                        if(!is_dir($fileNameWithoutZip))
                            mkdir($fileNameWithoutZip);
							if ($dh = opendir ( $fileNameWithoutZip )) {
								while ( ($file = readdir ( $dh )) !== false ) {
									echo "filename: " . $file;
									
									/**
									 */
									$fp = fopen ( $fileNameWithoutZip . DS . $file, 'r' );
									$content = fread ( $fp, filesize ( $fileNameWithoutZip . DS . $file ) );
									
									$fileType = pathinfo ( $fileNameWithoutZip . DS . $file, PATHINFO_EXTENSION );
									fclose ( $fp );
									/**
									 */
									if ($content) {
										$pinModel = Mage::getModel ( 'pin/pin' );
										$pinModel->setData ( 'fileblob', $content );
										$pinModel->setData ( 'filetype', $fileType );
										$pinModel->setData ( 'status', HN_Pin_Model_Pin::STATUS_AVAILABLE );
										if (isset ( $data ['productid'] )) {
											$productModel = Mage::getModel ( 'catalog/product' )->load ( $data ['productid'] );
											$pinModel->setData ( 'product_id', $data ['productid'] );
											//$pinModel->setData ( 'invoice_id', $data ['pin_invoice_id'] );
											$pinModel->setData ( 'product_name', $productName = $productModel->getName () );
										}
										
										$pinModel->save ();
									}
									
									/**
									 */
									if (is_file ( $fileNameWithoutZip . DS . $file ))
										unlink ( $fileNameWithoutZip . DS . $file );
								
								/**
								 */
								}
								closedir ( $dh );
							}
						//}
					/**
					 */
					} else {
					}
				/**
				 */
					$this->_redirect ( '*/*/index' );
						
				} catch ( Exception $e ) {
					Mage::logException ( $e );
			Mage::getSingleton ( 'adminhtml/session' )->addError ($e->getMessage() .$this->__('Please check the permission of media/import_zip folder') );
			$this->_redirect ( '*/*/index' );
				
				}
			}
			
			/**
			 */
			$storeId = Mage::app ()->getStore ()->getId ();
			$pin_is_qty_sync = Mage::getStoreConfig ( 'pin/general/qty_sync', $storeId );
			
			if ($pin_is_qty_sync == 1) {
				if (isset ( $data ['productid'] )) {
					
					$qty = Mage::helper ( 'pin' )->getQtyPINAvail ( $data ['productid'] );
					$this->syncQty ( $data ['productid'], $qty );
				}
			}
			// echo "file name ".$fileName;
		}
	
	/**
	 * end of upload file to a folder
	 */
	}
	
	/**
	 * save the images
	 */
	public function saveimageAction() {
		$data = $this->getRequest ()->getParams ();
		//Mage::log ( $data, null, 'pin.log', true );
		//Mage::log ( $_FILES, null, 'pin.log', true );

		if (isset ( $data ['productid'] ) && $data ['productid'] > 0) {
			if ((isset ( $data ['ispinproduct'] )) && $data ['ispinproduct'] == 'on') {
				$is_pin_product = 1;
			} else {
				$is_pin_product = 0;
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $this->__ ( 'Please check the check box is PIN product first to add PIN' ) );
				$this->_redirect ( '*/*/index' );
				
				return;
			}
			//Mage::log ( $is_pin_product, null, 'pin.log', true );
			$ispinModel = Mage::getModel ( 'pin/ispinproduct' )->load ( $data ['productid'], 'product_id' );
			
			if (is_object ( $ispinModel )) {
				$ispinModel->setStatus ( $is_pin_product )->save ();
				$ispinModel->setData ( 'type', $data ['pin_type'] );
				$ispinModel->setProductId ( $data ['productid'] )->save ();
			} else {
				$ispinModel = Mage::getModel ( 'pin/ispinproduct' );
				$ispinModel->setData ( 'product_id', $data ['productid'] );
				$ispinModel->setData ( 'status', $is_pin_product );
				$ispinModel->setData ( 'type', $data ['pin_type'] );
				$ispinModel->save ();
			}
			
			if (isset ( $data ['productid'] )) {
				$productModel = Mage::getModel ( 'catalog/product' )->load ( $data ['productid'] );
			}
			
			$productName = $productModel->getName ();
			
			// pin is file
			if (isset ( $data ['pin_type'] ) && $data ['pin_type'] == 2) {
				if (isset ( $_FILES ['img'] ['name'] )) {
					
					foreach ( $_FILES ['img'] ['name'] as $key => $value ) {
						if (isset ( $_FILES ['img'] ['tmp_name'] [$key] ) && $_FILES ['img'] ['tmp_name'] [$key] != '') {
							$path = Mage::getBaseDir ( 'media' ) . DS;
							$file_name = $path . $value;
							$fileType = $_FILES ['img'] ['type'] [$key];
							$ext = pathinfo ( $_FILES ['img'] ['name'] [$key], PATHINFO_EXTENSION );
							
							$fp = fopen ( $_FILES ['img'] ['tmp_name'] [$key], 'r' );
							$content = fread ( $fp, filesize ( $_FILES ['img'] ['tmp_name'] [$key] ) );
							fclose ( $fp );
							
							$pinModel = Mage::getModel ( 'pin/pin' );
							$pinModel->setData ( 'fileblob', $content );
							$pinModel->setData ( 'filetype', $fileType );
							$pinModel->setData ( 'file', $value );
							$pinModel->setData ( 'ext', $ext );
							$pinModel->setData ( 'status', HN_Pin_Model_Pin::STATUS_AVAILABLE );
							if (isset ( $data ['productid'] )) {
								$pinModel->setData ( 'product_id', $data ['productid'] );
                                $pinModel->setInvoiceId ($data ['pin_invoice_id'] );
								$pinModel->setData ( 'product_name', $productName = $productModel->getName () );
							}
							
							$pinModel->save ();
						}
					}
				}
			} elseif (isset ( $data ['pin_type'] ) && $data ['pin_type'] == 1) {
				if (! empty ( $data ['pin'] )) {
					foreach ( $data ['pin'] as $pin ) {
						
						if ($pin != '') {
							$pinencrypt = Mage::helper ( 'core' )->encrypt ( $pin );
							$pinModel = Mage::getModel ( 'pin/pin' );
							
							$pinModel->setData ( 'pin_number', $pinencrypt );
							$pinModel->setData ( 'filetype', HN_Pin_Model_Pin::TEXT_TYPE );
							$pinModel->setData ( 'status', HN_Pin_Model_Pin::STATUS_AVAILABLE );
							if (isset ( $data ['productid'] )) {
								$pinModel->setData ( 'product_id', $data ['productid'] );
                                $pinModel->setInvoiceId ($data ['pin_invoice_id'] );
								$pinModel->setData ( 'product_name', $productName = $productModel->getName () );
							}
							
							$pinModel->save ();
						}
					}
				}
			} // end elseif
			
			/**
			 */
			$storeId = Mage::app ()->getStore ()->getId ();
			$pin_is_qty_sync = Mage::getStoreConfig ( 'pin/general/qty_sync', $storeId );
            if (isset ( $data ['productid'] )) {
                Mage::helper("kontentaCw")->synchProductIdQty($data ['productid']);
            }
			if ($pin_is_qty_sync == 1) {
				if (isset ( $data ['productid'] )) {
					$qty = Mage::helper ( 'pin' )->getQtyPINAvail ( $data ['productid'] );
					$this->syncQty ( $data ['productid'], $qty );
				}
			}
			$this->_redirect ( 'pin/adminhtml_pin/index' );
		} else {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( $this->__ ( 'Please save product first then swith to edit product to add PIN)' ) );
			$this->_redirect ( '*/*/index' );
			return;
		}
	}
	
	/**
	 */
	public function viewfileAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$orderPin = Mage::getModel ( 'pin/orderpin' )->load ( $id );
		$file_content = $orderPin->getFileblob ();
		$filetype = $orderPin->getFiletype ();
		if ($filetype == '')
			$filetype = 'application/octet-stream';
		return $this->_prepareDownloadResponse ( $orderPin->getProductName (), $file_content, $filetype );
	}
	
	/**
	 */
	public function viewfilepinAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$orderPin = Mage::getModel ( 'pin/pin' )->load ( $id );
		$file_content = $orderPin->getFileblob ();
		$filetype = $orderPin->getFiletype ();
		if ($filetype == '')
			$filetype = 'application/octet-stream';
		return $this->_prepareDownloadResponse ( $orderPin->getFile(), $file_content, $filetype );
	}
	public function syncStockAction() {
		$product = Mage::getModel ( 'catalog/product' );
	}
	/**
	 *
	 * @param unknown_type $pin        	
	 * @param unknown_type $sku        	
	 * @param unknown_type $status        	
	 */
	private function _validateCsvTxt($pin, $sku, $status) {
	}
	
	/**
	 * send the pin
	 */
	public function massSendPinAction() {
		$data = $this->getRequest ()->getParams ();
		$orderIds = $data ['order_ids'];
		foreach ( $orderIds as $orderId ) {
			
			$order = Mage::getModel ( 'sales/order' )->load ( $orderId );
			$order_increment_id = $order->getIncrementId (); // Mage_Sales_Model_Order
			$items = $order->getAllItems ();
			$itemcount = count ( $items );
			$name = array ();
			$unitPrice = array ();
			$sku = array ();
			$ids = array ();
			$qty = array ();
			foreach ( $items as $itemId => $item ) {
				// echo $item->getId();
				// echo "<br>";
				// echo $item->getQtyToInvoice();
				// echo "<br>";
				// echo $item->getStatusId();
				// echo "<br>";
				$name [] = $item->getName ();
				$unitPrice [] = $item->getPrice ();
				$sku [] = $item->getSku ();
				$ids [] = $item->getProductId ();
				$qty [] = $item->getQtyToInvoice ();
				
				if ($item->getStatusId () == Mage_Sales_Model_Order_Item::STATUS_INVOICED) {
					$qty = $item->getQtyToInvoice ();
					// var_dump($qty);
					
					/**
					 * get the qty of the oder pin which have $order_item_id
					 */
					/**
					 * if the qty < then add new and send mail*
					 */
					$oderpinDatas = Mage::getResourceModel ( 'pin/orderpin_collection' )->getPinByOrderItemId ( $item->getId () );
					
					if (! empty ( $oderpinDatas )) {
						if (count ( $oderpinDatas ) < $qty) {
							/**
							 * $qty_more = $qty - count($oderpinDatas)
							 */
							$qty_more = $qty - count ( $oderpinDatas );
							$productId = $item->getProductId ();
							$this->_deliverPin ( $productId, $qty_more, $item->getId (), $orderId, $order_increment_id );
						} else {
							// echo "enought ";
						}
					}
				}
			}
		}
	}
	/**
	 *
	 * @param unknown_type $productId        	
	 * @param unknown_type $qty        	
	 */
	protected function _deliverPin($productId, $qty, $order_item_id, $order_id, $order_increment_id) {
		$oderpin_Model = Mage::getModel ( 'pin/orderpin' );
		$product = Mage::getModel ( 'catalog/product' )->load ( $productId );
		$product_name = $product->getName ();
		$product_sku = $product->getSku ();
		
		$pinifoArr = Mage::getResourceModel ( 'pin/ispinproduct' )->getPinTypeByProductId ( $productId );
		$type = $pinifoArr [0] ['type'];
		
		$oderpinData = array (
				'order_id' => $order_id,
				'order_increment_id' => $order_increment_id,
				'customer_id' => $customer_id,
				'product_name' => $product_name,
				'product_sku' => $product_sku 
		);
		
		if ($type == 1) { // text
			
			$pintextDatas = Mage::getResourceModel ( 'pin/pin_collection' )->getAvailTxtPin ( $qty );
			foreach ( $pintextDatas as $pinTxtData ) {
				$oderpinData ['pin_number'] = $pinTxtData ['pin_number'];
				$oderpinData ['filetype'] = $pinTxtData ['filetype']; // filetype
			}
			
			$oderpin_Model->setData ( $oderpinData )->save ();
		} elseif ($type == 2) { // file
			$pinfileDatas = Mage::getResourceModel ( 'pin/pin_collection' )->getAvailTxtPin ( $qty );
			
			foreach ( $pinfileDatas as $pinFileData ) {
				$oderpinData ['fileblob'] = $pinTxtData ['fileblob'];
				$oderpinData ['filetype'] = $pinTxtData ['filetype'];
			}
			
			$oderpin_Model->setData ( $oderpinData )->save ();
		}
	}
	protected function syncQty($productId, $qty) {
		$product = Mage::getModel ( 'catalog/product' )->load ( $productId );
		$product->setStockData ( array (
				'is_in_stock' => 1,
				'qty' => $qty,
				'manage_stock' => 1 
		) );
		$product->save ();
	}
	
	/**
	 * pinproduct page allows admin to manage products which has pin
	 */
	public function pinproductAction() {
		$this->_title ( $this->__ ( 'Pin product management' ) );
		$this->loadLayout ()->_setActiveMenu ( 'catalog/product' );
		$this->_addContent ( $this->getLayout ()->createBlock ( 'pin/adminhtml_pin_product_grid' ) );
		$this->renderLayout ();
	}

    public function searchAjaxCWProductsAction(){
        $value = $this->getRequest()->getPost("value");
        $results = Mage::helper("apiplugin")->getProductsBySubStr($value);
        echo json_encode($results);
    }

    public function confirmCorrespondingProductAction(){
        echo "hello";
    }

    public function notsyncedAction(){
        $this->_initAction();
        $this->renderLayout();
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('pin/pinitems');
        return $this;
    }
}
