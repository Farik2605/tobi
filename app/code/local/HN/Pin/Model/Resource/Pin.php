<?php 
class HN_Pin_Model_Resource_Pin extends  Mage_Core_Model_Resource_Db_Abstract {
	 /**
     * Initialize main table and table id field
     */
    protected function _construct()
    {
        $this->_init('pin/pin', 'id');
    }
    /**
     * 
     * @param unknown_type $ruleId
     */
    public function getPinByProductId($productId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('pin/pin'), 'id')
            ->where('product_id=?', $productId);

        return $read->fetchCol($select);
    }

    /**
     * Remove catalog rules product prices for specified date range and product
     *
     * @param int|string $fromDate
     * @param int|string $toDate
     * @param int|null $productId
     *
     * @return Mage_CatalogRule_Model_Resource_Rule
     */
    public function removeCatalogPricesForDateRange($fromDate, $toDate, $productId = null)
    {
        $write = $this->_getWriteAdapter();
        $conds = array();
        $cond = $write->quoteInto('rule_date between ?', $this->formatDate($fromDate));
        $cond = $write->quoteInto($cond.' and ?', $this->formatDate($toDate));
        $conds[] = $cond;
        if (!is_null($productId)) {
            $conds[] = $write->quoteInto('product_id=?', $productId);
        }

        /**
         * Add information about affected products
         * It can be used in processes which related with product price (like catalog index)
         */
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getTable('catalogrule/rule_product_price'), 'product_id')
            ->where(implode(' AND ', $conds))
            ->group('product_id');

        $replace = $write->insertFromSelect(
            $select,
            $this->getTable('catalogrule/affected_product'),
            array('product_id'),
            true
        );
        $write->query($replace);
        $write->delete($this->getTable('catalogrule/rule_product_price'), $conds);
        return $this;
    }
    
	
}
