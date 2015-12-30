<?php
class HN_Pin_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct ();
		$this->setId ( 'pin_product' );
		$this->setDefaultSort ( 'entity_id' );
		$this->setUseAjax ( false );
		$this->setFilterVisibility(false);
	}
	protected function _prepareCollection() {
		$id = Mage::app ()->getRequest ()->getParam ( 'id' );
		$collection = Mage::getModel ( 'pin/pin' )->getCollection ()->addFilter ( 'product_id', $id );
		$this->setCollection ( $collection );
		return parent::_prepareCollection ();
	}
	protected function _prepareColumns() {
		$this->addColumn ( 'id', array (
				'header' => Mage::helper ( 'pin' )->__ ( 'ID' ),
				'align' => 'right',
				'width' => '50px',
				'index' => 'id' 
		) );
		
		$this->addColumn ( 'product_name', array (
				'header' => Mage::helper ( 'pin' )->__ ( 'Product Name' ),
				'align' => 'right',
				'width' => '50px',
				'index' => 'product_name' 
		) );
		
		$this->addColumn ( 'filetype', array (
				'header' => Mage::helper ( 'pin' )->__ ( 'File Type' ),
				'align' => 'right',
				'width' => '50px',
				'index' => 'filetype' 
		) );
		
		$options = array (
				HN_Pin_Model_Pin::STATUS_AVAILABLE => HN_Pin_Model_Pin::STATUS_AVAILABLE,
				HN_Pin_Model_Pin::STATUS_EXPIRED => HN_Pin_Model_Pin::STATUS_EXPIRED,
				HN_Pin_Model_Pin::STATUS_SOLD_OUT => HN_Pin_Model_Pin::STATUS_SOLD_OUT 
		);
		
		$this->addColumn ( 'status', array (
				'header' => Mage::helper ( 'pin' )->__ ( 'Status' ),
				'align' => 'left',
				'width' => '80px',
				'index' => 'status',
				'type' => 'options',
				'options' => $options 
		) );

        $this->addColumn( 'invoice_id',array(
                'header' => Mage::helper('pin')->__('Invoice ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'invoice_id',
        ) );
		
		$this->addColumn ( 'pin', array (
				'header' => Mage::helper ( 'pin' )->__ ( 'PIN' ),
				'align' => 'left',
				'width' => '80px',
				'index' => 'pin_number',
				'renderer' => 'HN_Pin_Block_Adminhtml_Pin_Render',
				'filter'    => false,
				'sortable'  => false,
		) );
	}
	public function getRowUrl($row) {
		return false;
	}
}
