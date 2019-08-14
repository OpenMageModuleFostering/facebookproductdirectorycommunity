<?php
function getCategories(){
		$urlXML = 'http://facebook.magestore.com/category.php';
		$doc = new DOMDocument();
		$doc->load($urlXML);
		$xml = simplexml_load_string($doc->saveXML());
		$categories = array();
		foreach($xml->category as $category){
			$categories[(string)$category->category_id] = (string)$category->category_name;

		}
		return $categories;		
}
class Magestore_Facebook_Model_Category extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function toOptionArray()
    {
		$categories = getCategories();
        return $categories;
    }
}