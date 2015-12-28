<?php

use CodesWholesaleFramework\Postback\UpdatePriceAndStock\ProductUpdater;


class CodesWholesale_ApiPlugin_Update_MageUpdatePriceAndStock implements ProductUpdater
{
    public function updateProduct($cwProductId, $quantity, $priceSpread)
    {
        $product = Mage::getModel('catalog/product')->loadByAttribute('codeswholesale_product_id', $cwProductId);

        if ($product) {

            $product->setPrice($priceSpread);
            $product->save();

            $productId = $product->getId();
            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);

            $stockItem->setData('qty', $quantity);
            $stockItem->setData('is_in_stock', 1);

            $stockItem->save();
        }
    }
}