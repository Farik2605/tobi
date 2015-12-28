<?php
use CodesWholesaleFramework\Orders\Utils\DataBaseExporter;

class CodesWholesale_ApiPlugin_Exporters_MageDataBaseExporter implements DataBaseExporter
{

    public function export($item, $orderDataArray, $item_key)
    {
        $item->setLinks(json_encode($orderDataArray['links']));
        $item->setCwOrderId($orderDataArray['cwOrderId']);
        $item->setNumberOfPreorder($orderDataArray['preOrders']);
        $item->save();
    }
}