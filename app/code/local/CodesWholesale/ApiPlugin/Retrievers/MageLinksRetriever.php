<?php

/**
 * Created by PhpStorm.
 * User: maciejklowan
 * Date: 11/09/15
 * Time: 10:11
 */
class CodesWholesale_ApiPlugin_Retrievers_MageLinksRetriever
{

    public function links($item){

        return json_decode($item->getLinks());
    }

}