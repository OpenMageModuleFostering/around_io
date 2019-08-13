<?php
class AroundIo_Apiext_Model_ApiCall_Api
{
    public function productDetails($input)
    {
        
        $var = json_decode($input);
        $allproducts = Mage::getResourceModel('reports/product_collection')->setPageSize($var->limit)->setCurPage($var->offset);
        $productlist = array();
        $details = Mage::getModel('catalog/product');
        $prefix = Mage::getConfig()->getTablePrefix();
        //// $tags = Mage::getModel('tag/tag');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $getalltagsql        = "SELECT ".$prefix."tag_relation.product_id, ".$prefix."tag.name FROM ".$prefix."tag, ".$prefix."tag_relation where ".$prefix."tag.tag_id = ".$prefix."tag_relation.tag_id";
        $tagrows       = $connection->fetchAll($getalltagsql);
        $getallmediasql        = "SELECT ".$prefix."catalog_product_entity_media_gallery.value, ".$prefix."catalog_product_entity_media_gallery.entity_id, ".$prefix."catalog_product_entity_media_gallery.value_id, ".$prefix."catalog_product_entity_media_gallery_value.position FROM ".$prefix."catalog_product_entity_media_gallery, ".$prefix."catalog_product_entity_media_gallery_value where ".$prefix."catalog_product_entity_media_gallery.value_id = ".$prefix."catalog_product_entity_media_gallery_value.value_id";
        $mediarows       = $connection->fetchAll($getallmediasql);
         $t=array();$m=array();
        foreach ($allproducts as $product)
        {
            $lastId = $product['entity_id'];
            $details->load($product['entity_id']);
            foreach ($tagrows as $tag) {
                if($tag['product_id']==$product['entity_id']) {
                    $t[] = $tag['name'];
                }
            }
            foreach ($mediarows as $media) {
                if($media['entity_id']==$product['entity_id']) {
                    if($media['position']==1) {
                        $mainimgurl = '/media/catalog/product'.$media['value'];
                    }
                    $m[] = '/media/catalog/product'.$media['value'];
                }
            }
            $productlist[] = array(
                                  "price" => $details->getPrice()
                                , "name" => $details->getName()
                                , "sku" => $details->getSku()
                                , "url_path" => $details['url_path']
                                , "short_description" => $details['short_description']
                                , "create_date" => date("Y-m-d h:i:s", strtotime($details['created_at']))
                                , "update_date" => date("Y-m-d h:i:s", strtotime($details['updated_at']))
                                , "status" => $details['status']
                                , "tags" => implode(",", $t)
                                , "mainimageurl" => $mainimgurl
                                , "allimagesurl" => implode(",", $m)
                                );
            $t = null;
            $m = null;
            $mainimgurl = null;
        }

        return array('lastId'=>$lastId, 'data'=>$productlist);
    }

    public function debugging()
    {
        $debugDetails = array();
        
        //clear cache
        try {
            Mage::app()->cleanCache();
            $clearcache = 1;
        } catch (Exception $e) {
            $clearcache =0;
        }
        
        //reindex
        try {
            for ($i = 1; $i <= 9; $i++) {
                $process = Mage::getModel('index/process')->load($i);
                $process->reindexAll();
            }
            $reindexing = 1;
        } catch (Exception $e) {
            $reindexing = 0;             
        }
        
        //db name
        $dbname = Mage::getConfig()->getResourceConnectionConfig('default_setup')->dbname;
        
        //version
        $magentoVersion = Mage::getVersion();
        
        //prefix
        $prefix = Mage::getConfig()->getTablePrefix();

        //number of products
        $products = Mage::getResourceModel('reports/product_collection')->addAttributeToSelect('*');
        $count = count($products);

        $debugDetails = array('cleancache' => $clearcache, 'reindexing' => $reindexing, 'dbname' => $dbname, 'version' => $magentoVersion, 'DBprefix' => $prefix, 'productcount' => $count);
        
        return $debugDetails;
    }
    
}





?>