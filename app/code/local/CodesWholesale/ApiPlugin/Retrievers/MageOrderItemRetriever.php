<?php

use CodesWholesaleFramework\Postback\Retriever\ItemRetrieverInterface;

class CodesWholesale_ApiPlugin_Retrievers_MageOrderItemRetriever implements ItemRetrieverInterface
{

    /**
     * @param $orderData
     * @return array
     * @throws Exception
     */
    public function retrieveItem($orderData)
    {
        $cwProductId = Mage::getResourceModel('catalog/product')->getAttributeRawValue($orderData['item']->getProductId(), 'codeswholesale_product_id', $orderData['order']->getStoreId());

        $qty = intval($orderData['item']->getQtyOrdered());

        if (empty($cwProductId)) {

            throw new Exception("This product is not merged with any from CodesWholesale.");
        }

        $retrievedItems = array(
            'cwProductId' => $cwProductId,
            'qty' => $qty
        );
        return $retrievedItems;
    }
}