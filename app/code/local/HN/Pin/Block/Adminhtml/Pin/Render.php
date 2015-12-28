<?php 
class HN_Pin_Block_Adminhtml_Pin_Render extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		//$value = 'x' ;$row->getData($this->getColumn()->getIndex());
		$value = Mage::helper('core')->decrypt($row->getData($this->getColumn()->getIndex()));
		if ($value) {
		return '<span style="">'.$value.'</span>';
		} else {
			$url = 	 $this->getUrl('pin/adminhtml_pin/edit', array('id' => $row->getId()));
		
			return '<a href="'.$url .'">View file</a>';
		}
	}
}