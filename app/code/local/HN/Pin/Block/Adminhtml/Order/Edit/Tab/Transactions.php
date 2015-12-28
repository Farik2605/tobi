<?php
class HN_Pin_Block_Adminhtml_Order_Edit_Tab_Transactions
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    /**
     * Retrieve grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/sales_order/transactions', array('_current' => true));
    }

    /**
     * Retrieve grid row url
     *
     * @return string
     */
    public function getRowUrl($item)
    {
        return $this->getUrl('*/sales_transactions/view', array('_current' => true, 'txn_id' => $item->getId()));
    }

    /**
     * Retrieve tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Transactions');
    }

    /**
     * Retrieve tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Transactions');
    }

    /**
     * Check whether can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check whether tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return !Mage::getSingleton('admin/session')->isAllowed('sales/transactions/fetch');
    }
public function __construct()
	{
		parent::__construct();
		$this->setId('giftcerttransactionGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('pin/orderpin')->getCollection()->addFieldToFilter('id', Mage::registry('id'));
		//1

//$collection->addFieldToFilter('name', 'Product A');
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	protected function _prepareColumns()
	{
		 $this->addColumn('created_at', array(
            'header'    => Mage::helper('pin')->__('Created At'),
            'index'     => 'create_at',
            'width'     => 1,
            'type'      => 'datetime',
            'align'     => 'center',
          
            'html_decorators' => array('nobr')
        ));
        $this->addColumn('id', array(
            'header'    => Mage::helper('pin')->__('ID #'),
            'index'     => 'id',
            'type'      => 'number'
        ));
         $this->addColumn('action', array(
            'header'    => Mage::helper('pin')->__('Action'),
            'index'     => 'action',
            'width'     => 1,
            'type'      => 'options',
            'align'     => 'center',
            'options'   => array(
                'create'  => Mage::helper('pin')->__('create'),
                   'active'  => Mage::helper('pin')->__('active'),
                'spend'  => Mage::helper('pin')->__('spend'),
                
            )
            ))
        ;
		$this->addColumn('amount', array(
            'header'    => Mage::helper('pin')->__('Balance'),
            'index'     => 'balance',
            'type'      => 'number'
        ));
		
		 $this->addColumn('status', array(
            'header'    => Mage::helper('pin')->__('Status'),
            'index'     => 'status',
            'width'     => 1,
            'type'      => 'options',  //'2'=>'used', '3'=>'disable', '4'=>'expired'
            'align'     => 'center',
            'options'   => array(
                0  => Mage::helper('pin')->__('pending'),
                1  => Mage::helper('pin')->__('active'),
                2  => Mage::helper('pin')->__('used'),
                3  => Mage::helper('pin')->__('disable'),
                4  => Mage::helper('pin')->__('expired')
            )
            ))
        ;
        
          $this->addColumn('order_no', array(
            'header'    => Mage::helper('pin')->__('Order'),
            'index'     => 'order_no',
            'type'      => 'text'
        ));

        $this->addColumn('comment', array(
            'header'    => Mage::helper('pin')->__('Comment'),
            'index'     => 'comment',
            'type'      => 'text'
        ));
//		
//		
//		$this->addColumn('action',array(
//'header' => Mage::helper('pin')->__('Action'),
//'width' => '100',
//'type' => 'action',
//'getter' => 'getId',
//'actions' => array(array(
//'caption' => Mage::helper('pin')->__('Edit'),
//'url' => array('base'=> '*/*/edit'),
//'field' => 'id')),
//'filter' => false,
//'sortable' => false,
//'index' => 'stores',
//'is_system' => true,
//));

		return parent::_prepareColumns();
	}
	
}
