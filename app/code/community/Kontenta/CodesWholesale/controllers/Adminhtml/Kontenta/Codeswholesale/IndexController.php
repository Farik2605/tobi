<?php
class Kontenta_CodesWholesale_Adminhtml_Kontenta_Codeswholesale_IndexController extends Mage_Adminhtml_Controller_Action{
    public function submitAction(){
        $data = $this->getRequest()->getPost();
        $corr_id = $data["product"]["kontenta_corresponding_product"];
        $product_id = $data["productid"];
        if(!$product_id){
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper("apiplugin")->__("Can not find product Id"));
            $this->_redirectToProductGrid();
            return false;
        }
        if(!$corr_id){
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper("apiplugin")->__("You did not select corresponding item correctly."));
            $this->_redirectToProductView($product_id);
            return false;
        }
        if($product_id){
            $product = Mage::getModel("kontentacw/product")->load($product_id);
            foreach($data["product"] as $k=>$v){
                $product->setData($k,$v);
            }
            $product->synchronize();
            try{
                //echo $product->getCost();
                $product->save();
                Mage::getSingleton('adminhtml/session')->addSuccess("Corresponding product was attached successfully");
                $this->_redirectToProductView($product_id);
            }catch(Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirectToProductView($product_id);
            }
        }
    }

    protected function _redirectToProductView($product_id){
        $this->getResponse()->setRedirect(Mage::helper("adminhtml")->getUrl("adminhtml/catalog_product/edit",array("id"=>$product_id)));
    }

    protected function _redirectToProductGrid(){
        $this->getResponse()->setRedirect(Mage::helper("adminhtml")->getUrl("adminhtml/catalog_product/index"));
    }
}