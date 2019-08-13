<?php
class AroundIo_Apiext_Model_ApiCall_Api
{
    public function productDetails()
    {
        
         
        $allproducts = Mage::getResourceModel('reports/product_collection')
                                    ->addAttributeToSelect('*');
        $productlist = array();
         
        $details = Mage::getModel('catalog/product');
        // $tags = Mage::getModel('tag/tag');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $getalltagsql        = "SELECT tag_relation.product_id, tag.name FROM tag, tag_relation where tag.tag_id = tag_relation.tag_id";
        $tagrows       = $connection->fetchAll($getalltagsql);
        $getallmediasql        = "SELECT catalog_product_entity_media_gallery.value, catalog_product_entity_media_gallery.entity_id, catalog_product_entity_media_gallery.value_id, catalog_product_entity_media_gallery_value.position FROM catalog_product_entity_media_gallery, catalog_product_entity_media_gallery_value where catalog_product_entity_media_gallery.value_id = catalog_product_entity_media_gallery_value.value_id";
        $mediarows       = $connection->fetchAll($getallmediasql);
         $t=array();$m=array();
        foreach ($allproducts as $product)
        {
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
         
        return $productlist;
    }
    
}





?>