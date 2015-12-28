<?php
class HN_Pin_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct ();
		$this->setId ( 'pinGrid' );
		$this->setDefaultSort ( 'id' );
		$this->setSaveParametersInSession ( true );
	}
	protected function _prepareCollection() {
		$collection = Mage::getModel ( 'pin/orderpin_status' )->getCollection ();
		
		$orderTable = Mage::getSingleton('core/resource')->getTableName('sales/order');
		
		$flat_order_Tbl = Mage::getSingleton('core/resource')->getTableName('sales/order_grid');
		$collection->getSelect () -> joinInner ( array (
				'order' => $orderTable 
		), 'order.entity_id = main_table.order_id', array ('orderstatus'=>
				'order.status' , 'order_increment_id' =>'order.increment_id'
		) )->group ( 'main_table.item_id' );
		
		
		$this->setCollection ( $collection );
		return parent::_prepareCollection ();
	}
	protected function _prepareColumns() {
// 		$this->addColumn ( 'id', array (
// 				'header' => Mage::helper ( 'pin' )->__ ( 'ID' ),
// 				'align' => 'right',
// 				'width' => '50px',
// 				'index' => 'id' 
// 		) );
		
		$this->addColumn ( 'order_increment_id', array (
				'header' => Mage::helper ( 'pin' )->__ ( 'Order #' ),
				'align' => 'right',
				'width' => '50px',
				'index' => 'order_increment_id' ,
				'filter_index'=>'order.increment_id'
				
		) );
		
		$this->addColumn ( 'orderstatus', array (
				'header' => Mage::helper ( 'pin' )->__ ( 'Order status' ),
				'align' => 'right',
				'width' => '50px',
				'index' => 'orderstatus' ,
				'filter_index' =>'order.status'
		) );
		
		$this->addColumn ( 'product_name', array (
				'header' => Mage::helper ( 'pin' )->__ ( 'Product name' ),
				'align' => 'right',
				'width' => '50px',
				'index' => 'product_name' ,
				'filter_index' =>'main_table.product_name'
		) );
		
		$this->addColumn ( 'product_sku', array (
				'header' => Mage::helper ( 'pin' )->__ ( 'Product sku' ),
				'align' => 'right',
				'width' => '50px',
				'index' => 'product_sku' ,
				'filter_index' =>'main_table.product_sku'
		) );
		
		
		$this->addColumn ( 'total_qty', array (
				'header' => Mage::helper ( 'pin' )->__ ( 'Purchased Qty' ),
				'align' => 'right',
				'width' => '50px',
				'index' => 'total_qty' ,
				'filter_index' =>'main_table.total_qty'
		) );
		$this->addColumn ( 'delivery_qty', array (
				'header' => Mage::helper ( 'pin' )->__ ( 'Delivery Qty' ),
				'align' => 'right',
				'width' => '50px',
				'index' => 'delivery_qty' ,
				'filter_index' =>'main_table.delivery_qty'
		) );
		
		$options = array (
				0 =>Mage::helper('pin')->__('Not complete') ,
				1=>Mage::helper('pin')->__('Complete') ,
		);
		
		$this->addColumn ( 'delivery_status', array (
				'header' => Mage::helper ( 'pin' )->__ ( 'Delivery status' ),
				'align' => 'left',
				'width' => '80px',
				'index' => 'delivery_status',
				'type' => 'options',
				'filter_index' =>'main_table.delivery_status',
				'options' => $options 
		) );
		$this->addColumn('action',
				array(
						'header'    => Mage::helper('sales')->__('Detail'),
						'width'     => '50px',
						'type'      => 'action',
						'getter'     => 'getOrderId',
						'actions'   => array(
								array(
										'caption' => Mage::helper('pin')->__('Detail'),
										'url'     => array('base'=>'*/adminhtml_order/view'),
										'field'   => 'order_id'
								),
								
						),
						'filter'    => false,
						'sortable'  => false,
						'index'     => 'stores',
						'is_system' => true,
				));
		$this->addColumn('delivery',
				array(
						'header'    => Mage::helper('sales')->__('Delivery'),
						'width'     => '50px',
						'type'      => 'action',
						'getter'     => 'getItemId',
						'actions'   => array(
								array(
										'caption' => Mage::helper('pin')->__('Delivery PIN'),
										'url'     => array('base'=>'*/adminhtml_order/deliverypin'),
										'field'   => 'id'
								)
						),
						'filter'    => false,
						'sortable'  => false,
						'index'     => 'stores',
						'is_system' => true,
				));
		$this->addExportType ( '*/*/exportCsv', Mage::helper ( 'pin' )->__ ( 'CSV' ) );
		$this->addExportType ( '*/*/exportXml', Mage::helper ( 'pin' )->__ ( 'Excel XML' ) );
		return parent::_prepareColumns ();
	}
	public function getRowUrl($row) {
		return false;
// 		return $this->getUrl ( '*/*/edit', array (
// 				'id' => $row->getId () 
// 		) );
	}
	protected function _prepareMassaction() {
		$this->setMassactionIdField ( 'id' );
		$this->getMassactionBlock ()->setFormFieldName ( 'id' );
		$this->getMassactionBlock ()->setUseSelectAll ( false );
		
		if (Mage::getSingleton ( 'admin/session' )->isAllowed ( 'sales/order/actions/cancel' )) {
			$this->getMassactionBlock ()->addItem ( 'delete_pin', array (
					'label' => Mage::helper ( 'sales' )->__ ( 'Delete' ),
					'url' => $this->getUrl ( '*/adminhtml_pin/massDelete' ) 
			) );
		}
		
		$statuses = array (
				array (
						'label' => HN_Pin_Model_Pin::STATUS_AVAILABLE,
						'value' => HN_Pin_Model_Pin::STATUS_AVAILABLE 
				),
				array (
						'label' => HN_Pin_Model_Pin::STATUS_EXPIRED,
						'value' => HN_Pin_Model_Pin::STATUS_EXPIRED 
				),
				array (
						'label' => HN_Pin_Model_Pin::STATUS_SOLD_OUT,
						'value' => HN_Pin_Model_Pin::STATUS_SOLD_OUT 
				) 
		);
		
		$this->getMassactionBlock ()->addItem ( 'changestatus_pin', array (
				'label' => Mage::helper ( 'sales' )->__ ( 'Change Status' ),
				'url' => $this->getUrl ( '*/adminhtml_pin/changeStatus' ),
				'additional' => array (
						'visibility' => array (
								'name' => 'status',
								'type' => 'select',
								'class' => 'required-entry',
								'label' => Mage::helper ( 'catalog' )->__ ( 'Status' ),
								'values' => $statuses 
						) 
				) 
		) );
	}
}
