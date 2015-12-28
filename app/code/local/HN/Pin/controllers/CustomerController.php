<?php
/**
 *
 * @category   Inchoo
 * @package    Inchoo Featured Products
 * @author     Domagoj Potkoc, Inchoo Team <web@inchoo.net>
 */
class HN_Pin_CustomerController extends Mage_Core_Controller_Front_Action
{

	public function indexAction()
	{
		//$template = Mage::getConfig()->getNode('global/page/layouts/'.Mage::getStoreConfig("featuredproducts/general/layout").'/template');
		
		$this->loadLayout();		

//		$this->getLayout()->getBlock('root')->setTemplate($template);
//		$this->getLayout()->getBlock('head')->setTitle($this->__(Mage::getStoreConfig("featuredproducts/general/meta_title")));
//		$this->getLayout()->getBlock('head')->setDescription($this->__(Mage::getStoreConfig("featuredproducts/general/meta_description")));
//		$this->getLayout()->getBlock('head')->setKeywords($this->__(Mage::getStoreConfig("featuredproducts/general/meta_keywords")));
//		
		$this->renderLayout();
	}
public function testAction() {
		$tPrefix = (string) Mage::getConfig()->getTablePrefix();
	
	    $tbl_pin = $tPrefix.'orderpin';
		$query = 'select * from `' .$tbl_pin. '` ';
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		foreach ($rows as $row) {
			var_dump($row);
		}
		$pinInfo = "222222222222";
		$entity_id = 16;
		$query = 
		"UPDATE  `".$tbl_pin. "` SET  `pin_info` =  '". $pinInfo. "'
		
		 WHERE  `entity_id` =".$entity_id;
		//$db->query($query);
}
}