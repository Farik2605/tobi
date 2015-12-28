<?php
class HN_Pin_Block_Adminhtml_Pin_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct ();
		$this->setId ( 'pinproductGrid' );
		$this->setDefaultSort ( 'id' );
		$this->setSaveParametersInSession ( true );
	}
	protected function _prepareCollection() {
		// Zend_Db_Select
		$collection = Mage::getModel ( 'pin/pin' )->getCollection ();
		
		$prefix = Mage::getConfig ()->getTablePrefix ();
		$catalog_product_Tbl = $prefix . 'catalog_product_entity';
		$ispin_product_Tbl = $prefix . 'ispinproduct';
		// $collection->getSelect ()->columns(array('status','product_id','total'=>'COUNT(product_id)'));
		// $collection->getSelect ()->joinInner ( array (
		// 'product' => $catalog_product_Tbl
		// ), 'product.entity_id = main_table.product_id', array (
		// 'product.sku'
		// ) );
		
		// $collection->getSelect ()->group ( 'entity_id' );
		
		// $collection->getSelect ()->columns(array('status','product_id','total'=>'COUNT(DISTINCT main_table.id)'))
		// ->where('main_table.status=?', 'available')->orwhere('main_table.status="sold_out"')
		// ->join( array (
		// 's' => 'pin'),
		// 's.id = main_table.id and s.status ="sold_out" or main_table.status="available"',
		// array (
		// 's.status' ,'sold'=>'count(DISTINCT s.id)'
		// ) )->group('main_table.product_id');
		// ;
		$collection->getSelect ()->columns ( array (
				'total' => 'COUNT(DISTINCT main_table.id)' ,'product_id'=>'main_table.product_id'
		) )->joinInner ( array (
				'product' => $catalog_product_Tbl 
		), 'product.entity_id = main_table.product_id', array (
				'product.sku' 
		) )
		->joinInner ( array (
		'ispinproduct' => $ispin_product_Tbl
		), 'ispinproduct.product_id= main_table.product_id', array ('enable'=>
		'ispinproduct.status'
				) )
		->group ( 'main_table.product_id' );
		$this->setCollection ( $collection );
		//var_dump($collection->getLastItem()->getData());
		return parent::_prepareCollection ();
	}
	protected function _prepareColumns() {
// 		$this->addColumn ( 'id', array (
// 				'header' => Mage::helper ( 'pin' )->__ ( 'ID' ),
// 				'align' => 'right',
// 				'width' => '50px',
// 				'index' => 'id' 
// 		) );
		
		$this->addColumn ( 'product_id', array (
				'header' => Mage::helper ( 'pin' )->__ ( 'Product Id' ),
				'align' => 'right',
				'width' => '50px',
				'index' => 'product_id' ,
				'filter_index'=>'main_table.product_id'
				
		) );
		
		$this->addColumn ( 'product_name', array (
				'header' => Mage::helper ( 'pin' )->__ ( 'Product Name' ),
				'align' => 'right',
				'width' => '50px',
				'index' => 'product_name' ,
		) );
		$this->addColumn ( 'sku', array (
				'header' => Mage::helper ( 'pin' )->__ ( 'Sku' ),
				'align' => 'right',
				'width' => '50px',
				'index' => 'sku',
				'filter_index' => 'product.sku' 
		) );
		$this->addColumn ( 'enable', array (
				'header' => Mage::helper ( 'pin' )->__ ( 'Enable' ),
				'align' => 'right',
				'width' => '50px',
				'index' => 'enable',
				'filter_index' => 'ispinproduct.status' ,
				'type' =>'options',
				'options'=>array(0=>Mage::helper('pin')->__('disable'), 1=>Mage::helper('pin')->__('enable'))
		) );
		$this->addColumn ( 'total', array (
				'header' => Mage::helper ( 'pin' )->__ ( 'Qty' ),
				'align' => 'right',
				'width' => '50px',
				'index' => 'total',
				 'filter'    => false,
      'sortable'  => false 
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
				'index' =>'status',
				'filter_index' => 'main_table.status',
				'type' => 'options',
				'options' => $options 
		) );
		return parent::_prepareColumns ();
	}
	public function getRowUrl($row) {
		return $this->getUrl ( 'adminhtml/catalog_product/edit', array (
				'id' => $row->getProductId () 
		) );
	}
}
