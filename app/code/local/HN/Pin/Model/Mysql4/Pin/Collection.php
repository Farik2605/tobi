<?php



class HN_Pin_Model_Mysql4_Pin_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract

{

	public function _construct()

	{

		parent::_construct();

		$this->_init('pin/pin');

	}





	/**

	 * @return array

	 */

	public function getActivePin($product_id) {



		$this->getSelect()->where('product_id=?', $product_id);

		$this->getSelect()->where('status=?', HN_Pin_Model_Pin::STATUS_AVAILABLE);

		// return  $this->fetchItem();

		return $this->getData();

	}



	/**

	 * @return array

	 */

	public function getAvailPin($qty) {



		$this->getSelect()->where('status=?', HN_Pin_Model_Pin::STATUS_AVAILABLE)->limit($qty);

		// return  $this->fetchItem();

		return $this->getData();

	}





	/**

	 * @return array

	 */

	public function getAvailTxtPin($qty) {



		$this->getSelect()->where('status=?', HN_Pin_Model_Pin::STATUS_AVAILABLE);

		$this->getSelect()->where('filetype=?', HN_Pin_Model_Pin::TEXT_TYPE)->limit($qty);

		// return  $this->fetchItem();

		return $this->getData();

	}



	/**

	 * @return array

	 */

	public function getAvailFilePin($qty) {



		$this->getSelect()->where('status=?', HN_Pin_Model_Pin::STATUS_AVAILABLE);

		$this->getSelect()->where('filetype !=?', HN_Pin_Model_Pin::TEXT_TYPE)->limit($qty);

		// return  $this->fetchItem();

		return $this->getData();

	}



	/**

	 *

	 */

	public function getQtyAvailFilePin() {



		$this->getSelect()->where('status=?', HN_Pin_Model_Pin::STATUS_AVAILABLE);

		$this->getSelect()->where('filetype !=?', HN_Pin_Model_Pin::TEXT_TYPE);

		// return  $this->fetchItem();

		return $this->getData();

	}



	public function getQtyAvailTextPin() {



		$this->getSelect()->where('status=?', HN_Pin_Model_Pin::STATUS_AVAILABLE);

		$this->getSelect()->where('filetype =?', HN_Pin_Model_Pin::TEXT_TYPE);

		// return  $this->fetchItem();

		return $this->getData();

	}



	public function getLowStock($limit,$productid) {

		$this->getSelect()->where('main_table.status=?', HN_Pin_Model_Pin::STATUS_AVAILABLE);

		//$this->getSelect()->where('filetype =?', HN_Pin_Model_Pin::TEXT_TYPE);

		$this->getSelect()->where('main_table.product_id=?', $productid);

		$this->getSelect()->join(array('t2' => $this->getTable('pin/ispinproduct')) ,'t2.product_id = main_table.product_id' );

		$this->getSelect()->group('main_table.id');

		// return  $this->fetchItem();

		//if ($this->getSize() < $limit) {

			

		//}

		return $this->getData();

		//return $this;

	}



	/**

	 *

	 */

	public function addFieldsToFilter($fields)

	{

		if ($fields) {

			$previousSelect = null;

			$conn = $this->getConnection();

			foreach ($fields as $table => $conditions) {

				foreach ($conditions as $attributeId => $conditionValue) {

					$select = $conn->select();

					$select->from(array('t1' => $table), 'entity_id');

					$conditionData = array();



					if (!is_numeric($attributeId)) {

						$field = 't1.'.$attributeId;

					}

					else {

						$storeId = $this->getStoreId();

						$onCondition = 't1.entity_id = t2.entity_id'

						. ' AND t1.attribute_id = t2.attribute_id'

						. ' AND t2.store_id=?';



						$select->joinLeft(

						array('t2' => $table),

						$conn->quoteInto($onCondition, $storeId),

						array()

						);

						$select->where('t1.store_id = ?', 0);

						$select->where('t1.attribute_id = ?', $attributeId);



						if (array_key_exists('price_index', $this->getSelect()->getPart(Varien_Db_Select::FROM))) {

							$select->where('t1.entity_id = price_index.entity_id');

						}



						$field = $this->getConnection()->getCheckSql('t2.value_id>0', 't2.value', 't1.value');



					}



					if (is_array($conditionValue)) {

						if (isset($conditionValue['in'])){

							$conditionData[] = array('in' => $conditionValue['in']);

						}

						elseif (isset($conditionValue['in_set'])) {

							$conditionParts = array();

							foreach ($conditionValue['in_set'] as $value) {

								$conditionParts[] = array('finset' => $value);

							}

							$conditionData[] = $conditionParts;

						}

						elseif (isset($conditionValue['like'])) {

							$conditionData[] = array ('like' => $conditionValue['like']);

						}

						elseif (isset($conditionValue['from']) && isset($conditionValue['to'])) {

							$invalidDateMessage = Mage::helper('catalogsearch')->__('Specified date is invalid.');

							if ($conditionValue['from']) {

								if (!Zend_Date::isDate($conditionValue['from'])) {

									Mage::throwException($invalidDateMessage);

								}

								if (!is_numeric($conditionValue['from'])){

									$conditionValue['from'] = Mage::getSingleton('core/date')

									->gmtDate(null, $conditionValue['from']);

									if (!$conditionValue['from']) {

										$conditionValue['from'] = Mage::getSingleton('core/date')->gmtDate();

									}

								}

								$conditionData[] = array('gteq' => $conditionValue['from']);

							}

							if ($conditionValue['to']) {

								if (!Zend_Date::isDate($conditionValue['to'])) {

									Mage::throwException($invalidDateMessage);

								}

								if (!is_numeric($conditionValue['to'])){

									$conditionValue['to'] = Mage::getSingleton('core/date')

									->gmtDate(null, $conditionValue['to']);

									if (!$conditionValue['to']) {

										$conditionValue['to'] = Mage::getSingleton('core/date')->gmtDate();

									}

								}

								$conditionData[] = array('lteq' => $conditionValue['to']);

							}



						}

					} else {

						$conditionData[] = array('eq' => $conditionValue);

					}





					foreach ($conditionData as $data) {

						$select->where($conn->prepareSqlCondition($field, $data));

					}



					if (!is_null($previousSelect)) {

						$select->where('t1.entity_id IN (?)', new Zend_Db_Expr($previousSelect));

					}

					$previousSelect = $select;

				}

			}

			$this->addFieldToFilter('entity_id', array('in' => new Zend_Db_Expr($select)));

		}



		return $this;

	}



	public function getActivePinTextByProduct($product_id) {



		$this->getSelect()->where('product_id=?', $product_id);

		$this->getSelect()->where('status=?', HN_Pin_Model_Pin::STATUS_AVAILABLE);

		$this->getSelect()->where('filetype =?', HN_Pin_Model_Pin::TEXT_TYPE);

		// return  $this->fetchItem();

		return $this;

	}



	public function getActivePinFileByProduct($product_id) {



		$this->getSelect()->where('product_id=?', $product_id);

		$this->getSelect()->where('status=?', HN_Pin_Model_Pin::STATUS_AVAILABLE);

		$this->getSelect()->where('filetype !=?', HN_Pin_Model_Pin::TEXT_TYPE);

		// return  $this->fetchItem();

		return $this;

	}

	

	public function getActivePinTextByProductQty($product_id,$qty) {

	

		$this->getSelect()->where('product_id=?', $product_id);

		$this->getSelect()->where('status=?', HN_Pin_Model_Pin::STATUS_AVAILABLE);

		$this->getSelect()->where('filetype =?', HN_Pin_Model_Pin::TEXT_TYPE)->limit($qty);

		

		// return  $this->fetchItem();

		 return $this->getData();

	}

	

	public function getActivePinFileByProductQty($product_id, $qty) {

	

		$this->getSelect()->where('product_id=?', $product_id);

		$this->getSelect()->where('status=?', HN_Pin_Model_Pin::STATUS_AVAILABLE);

		$this->getSelect()->where('filetype !=?', HN_Pin_Model_Pin::TEXT_TYPE)->limit($qty);

		// return  $this->fetchItem();

		 return $this->getData();

	}

}
