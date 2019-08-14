<?php
class Magestore_Facebook_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	$product_limit = Mage::getStoreConfig('facebook/general/product_limit');
		$keyword = $this->getRequest()->getParam('keyword');
		$category = $this->getRequest()->getParam('category');
		$curPage = $this->getRequest()->getParam('page');
		$store_id = $this->getRequest()->getParam('store_id','');
		if((!$curPage)||($curPage == 0)){
			$curPage = 1;
		}
		$collection = Mage::getModel("catalog/product")->getCollection()
					->addAttributeToSelect("*")
					//->addFieldToFilter("name",array('like'=>'%'.$this->clean_url($keyword).'%'))
					->addFieldToFilter("status",1)			
					->setOrder('updated_at','DESC')
					;
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($collection);					
		if($category !='all'){
			$cat = Mage::getModel('catalog/category')
					->load($category);
			if($cat->getId()){
				$collection->addCategoryFilter($cat);
			}
		}	
		if($keyword !=''){
			$collection->addFieldToFilter("name",array('like'=>'%'.$this->clean_url($keyword).'%'));
		}	

		if($store_id !=''){
			$currentStore = Mage::app()->getStore()->load($store_id);
			if($currentStore->getId()){
				$collection->setStore($currentStore);	
			}
			
		}
		$totalRecord =$collection->getSize();
		$totalPage = round((int)$totalRecord/(int)$product_limit)+1;
		$collection->setCurPage($curPage);
		$collection->setPageSize($product_limit);

		$export = Mage::getModel("facebook/export");
		$export->setProductCollection($collection);
		$export->setPaginatorInfo($totalRecord,$totalPage,$curPage,$product_limit);
		echo $export->exportToXML();
    }
	public function countAction(){
		$collection = Mage::getModel("catalog/product")->getCollection()
					->addFieldToFilter('status',1);
		$export = Mage::getModel("facebook/export");
		echo $export->exportTotalProduct($collection->getSize());
	}
	public function viewAction(){
		$productId = $this->getRequest()->getParam('product');
		$product = Mage::getModel('catalog/product')->load($productId);
		$export = Mage::getModel('facebook/export');
		$export->setProduct($product);
		$collectionImage = $product->getMediaGalleryImages();
		echo $export->exportProductDetail();
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
    public function _addProductAttributesAndPrices(Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection)
    {
        return $collection
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes());
    }

	public function newproductAction(){
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        
        $collection = Mage::getResourceModel('catalog/product_collection');
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        
        $collection->addStoreFilter()
			->addAttributeToSelect("*")
            ->addAttributeToFilter('news_from_date', array('date' => true, 'to' => $todayDate))
            ->addAttributeToFilter('news_to_date', array('or'=> array(
                0 => array('date' => true, 'from' => $todayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToSort('news_from_date', 'desc')
            ->setPageSize(10)
            ->setCurPage(1)
        ;
		$export = Mage::getModel("facebook/export");
		$export->setProductCollection($collection);
		echo $export->exportToXML();		
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
	
	public function storeAction(){
		$collection = Mage::app()->getStore()->getCollection();
		$export = Mage::getModel('facebook/export');
		$xmlOutput = $export->exportStores($collection);
		print($xmlOutput);
	}

}