<?php
class Magestore_Facebook_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	$product_limit = Mage::getStoreConfig('facebook/general/product_limit');
		
		$keyword = $this->getRequest()->getParam('keyword');

		$collection = Mage::getModel("catalog/product")->getCollection()
					->addAttributeToSelect("*")
					->addFieldToFilter("name",array('like'=>'%'.$this->clean_url($keyword).'%'))
					->addFieldToFilter("status",1)			
					->setCurPage(1)
					->setPageSize($product_limit)
					->setOrder('updated_at','DESC')
					;

		$export = Mage::getModel("facebook/export");
		$export->setProductCollection($collection);
		echo $export->exportToXML();
    }
	public function countAction(){
		$collection = Mage::getModel("catalog/product")->getCollection()
					->addFieldToFilter('status',1);
		$export = Mage::getModel("facebook/export");
		echo $export->exportTotalProduct($collection->getSize());
	}	
	public function productAction()
	{
		$id = $this->getRequest()->getParam('id');
		
		$product = Mage::getModel("catalog/product")->load($id);
		if(!$product->getEnable())
		{
			die();
		}
		
		$collection = array();
		$collection[] = $product;
		
		$export = Mage::getModel("facebook/export");
		$export->setProductCollection($collection);
		echo $export->exportProductToXML();
	}
	
	public function allproductAction()
	{		
		$collection = Mage::getModel("catalog/product")->getCollection()
					->addAttributeToSelect("*")
					->addFieldToFilter("status",1)					   
					->setOrder('updated_at','DESC')
						;

		$export = Mage::getModel("facebook/export");
		$export->setProductCollection($collection);
		echo $export->exportAllProductToXML();
	}
	

	public function clean_url($text)
	{
		$text=strtolower($text);
		$code_entities_match = array(' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','.','/','*','+','~','`','=');
		$code_entities_replace = array('-','-','','','','','','','','','','','','','','','','','','','','','','','','');
		$text = str_replace($code_entities_match, $code_entities_replace, $text);
		return $text;
	}

	
	public function categoryAction()
	{		
		$collection = Mage::getModel("catalog/category")->getCollection()
					->addAttributeToSelect("*")
					->addFieldToFilter("is_active",1)					   
					->setOrder('name','ASC')
						;

			
		$export = Mage::getModel("facebook/export");
		$export->setProductCollection($collection);
		echo $export->exportAllCategoryToXML();
	}

}