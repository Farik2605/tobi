<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order transactions tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class HN_Giftcert_Block_Adminhtml_Giftcert_Edit_Tab_Transactions
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
		$collection = Mage::getModel('giftcert/giftcerttransaction')->getCollection()->addFieldToFilter('voucher_id', Mage::registry('voucherid'));
		//1

//$collection->addFieldToFilter('name', 'Product A');
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	protected function _prepareColumns()
	{
		 $this->addColumn('created_at', array(
            'header'    => Mage::helper('giftcert')->__('Created At'),
            'index'     => 'create_at',
            'width'     => 1,
            'type'      => 'datetime',
            'align'     => 'center',
          
            'html_decorators' => array('nobr')
        ));
        $this->addColumn('id', array(
            'header'    => Mage::helper('giftcert')->__('ID #'),
            'index'     => 'id',
            'type'      => 'number'
        ));
         $this->addColumn('action', array(
            'header'    => Mage::helper('giftcert')->__('Action'),
            'index'     => 'action',
            'width'     => 1,
            'type'      => 'options',
            'align'     => 'center',
            'options'   => array(
                'create'  => Mage::helper('giftcert')->__('create'),
                   'active'  => Mage::helper('giftcert')->__('active'),
                'spend'  => Mage::helper('giftcert')->__('spend'),
                
            )
            ))
        ;
		$this->addColumn('amount', array(
            'header'    => Mage::helper('giftcert')->__('Balance'),
            'index'     => 'balance',
            'type'      => 'number'
        ));
		
		 $this->addColumn('status', array(
            'header'    => Mage::helper('giftcert')->__('Status'),
            'index'     => 'status',
            'width'     => 1,
            'type'      => 'options',  //'2'=>'used', '3'=>'disable', '4'=>'expired'
            'align'     => 'center',
            'options'   => array(
                0  => Mage::helper('giftcert')->__('pending'),
                1  => Mage::helper('giftcert')->__('active'),
                2  => Mage::helper('giftcert')->__('used'),
                3  => Mage::helper('giftcert')->__('disable'),
                4  => Mage::helper('giftcert')->__('expired')
            )
            ))
        ;
        
          $this->addColumn('order_no', array(
            'header'    => Mage::helper('giftcert')->__('Order'),
            'index'     => 'order_no',
            'type'      => 'text'
        ));

        $this->addColumn('comment', array(
            'header'    => Mage::helper('giftcert')->__('Comment'),
            'index'     => 'comment',
            'type'      => 'text'
        ));
//		
//		
//		$this->addColumn('action',array(
//'header' => Mage::helper('giftcert')->__('Action'),
//'width' => '100',
//'type' => 'action',
//'getter' => 'getId',
//'actions' => array(array(
//'caption' => Mage::helper('giftcert')->__('Edit'),
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
