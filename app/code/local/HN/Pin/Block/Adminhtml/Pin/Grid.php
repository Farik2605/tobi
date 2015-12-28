<?php
class HN_Pin_Block_Adminhtml_Pin_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('pinGrid');
		$this->setDefaultSort('id');
		$this->setSaveParametersInSession(true);
	}
	
    
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('pin/pin')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	protected function _prepareColumns()
	{
		$this->addColumn('id',
		 array('header' => Mage::helper('pin')->__('ID'),
		 'align' =>'right','width' => '50px','index' => 'id',
		 ));
		 
		  $this->addColumn('product_id',
		 array('header' => Mage::helper('pin')->__('Product Id'),
		 'align' =>'right','width' => '50px','index' => 'product_id',
		 ));
		 
		 $this->addColumn('product_name',
		 array('header' => Mage::helper('pin')->__('Product Name'),
		 'align' =>'right','width' => '50px','index' => 'product_name',
		 ));
		 
		 $this->addColumn('filetype',
		 array('header' => Mage::helper('pin')->__('File Type'),
		 'align' =>'right','width' => '50px','index' => 'filetype',
		 ));
		 
		 
		 
		 $options = array(
		                 HN_Pin_Model_Pin::STATUS_AVAILABLE => HN_Pin_Model_Pin::STATUS_AVAILABLE,
		                 HN_Pin_Model_Pin::STATUS_EXPIRED => HN_Pin_Model_Pin::STATUS_EXPIRED,
		                 HN_Pin_Model_Pin::STATUS_SOLD_OUT =>HN_Pin_Model_Pin::STATUS_SOLD_OUT
		 );
		 
		 $this->addColumn('status', array('header' => Mage::helper('pin')->__('Status'),'align' => 'left',
										'width' => '80px',
										'index' => 'status',
										'type' => 'options',
										'options' => $options,
		                           ));
	
        $this->addExportType('*/*/exportCsv', Mage::helper('pin')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('pin')->__('Excel XML'));
		return parent::_prepareColumns();
	}
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
	
	  protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');
        $this->getMassactionBlock()->setUseSelectAll(false);

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
            $this->getMassactionBlock()->addItem('delete_pin', array(
                 'label'=> Mage::helper('sales')->__('Delete'),
                 'url'  => $this->getUrl('*/adminhtml_pin/massDelete'),
            ));
        }
        
        $statuses = array (
        array ('label'=>HN_Pin_Model_Pin::STATUS_AVAILABLE, 'value'=>HN_Pin_Model_Pin::STATUS_AVAILABLE) ,
        array('label'=>HN_Pin_Model_Pin::STATUS_EXPIRED, 'value'=>HN_Pin_Model_Pin::STATUS_EXPIRED),
         array('label'=>HN_Pin_Model_Pin::STATUS_SOLD_OUT, 'value'=>HN_Pin_Model_Pin::STATUS_SOLD_OUT)
        ) ;
        
         $this->getMassactionBlock()->addItem('changestatus_pin', array(
                 'label'=> Mage::helper('sales')->__('Change Status'),
                 'url'  => $this->getUrl('*/adminhtml_pin/changeStatus'),
                 'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalog')->__('Status'),
                         'values' => $statuses
                     )
            )));

    }
}
