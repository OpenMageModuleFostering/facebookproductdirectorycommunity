<?php

class Magestore_Facebook_Model_Export extends Mage_Core_Model_Abstract
{
	protected $_element ;
	protected $_simplerss;
	protected $_productCollection;
	protected $_product;
	protected $_totalRecord = -1;
	protected $_totalPage;
	protected $_currentPage =1;
	protected $_product_limit ;
	public function setProductCollection($collection){
		$this->_productCollection = $collection;
	}
	public function getProductCollection(){
		return $this->_productCollection;
	}
	
	public function setProduct($product)
	{
		$this->_product = $product;
	}
	
	public function getProduct($product)
	{
		return $this->_product;
	}
	
	public function exportTotalProduct($total){
		$this->initDom();
		$parentElement = $this->getDom();
		$totalProductElement = $parentElement->createElement('total',$total);
		$parentElement->appendChild($totalProductElement);
		$this->_simplerss->appendChild($parentElement);
		$dom = $this->_element;
		return $dom->saveXML();			
	}
	public function exportProductDetail(){
		$this->initDom();
		$_product = $this->_product;
		$name = htmlspecialchars($_product->getName());
		$shortDescription = htmlspecialchars($_product->getDescription());
		$price = $this->get_product_price($_product);
		$image = Mage::helper('catalog/image')->init($_product, 'image')->resize(400);
		$dom = $this->getDom();
		
		$productDetail = $dom->createElement('product');
		$priceElement  = $dom->createElement('price',htmlspecialchars($price));
		$nameElement   = $dom->createElement('name',htmlspecialchars($name));
		$linkElement 	= $dom->createElement('link',$_product->getProductUrl());
		$shortDescriptionElement = $dom->createElement('short_description',htmlspecialchars($shortDescription));
		$imageElement  = $dom->createElement('image',htmlspecialchars($image));
		$productIdElement = $dom->createElement('product_id',$_product->getId());
		$productDetail->appendChild($priceElement);
		$productDetail->appendChild($nameElement);
		$productDetail->appendChild($linkElement);
		$productDetail->appendChild($shortDescriptionElement);
		$productDetail->appendChild($imageElement);
		$productDetail->appendChild($productIdElement);
		$galleryCollection = $_product->getMediaGalleryImages();
		foreach($galleryCollection as $_image){
			$image = Mage::helper('catalog/image')->init($_product, 'image',$_image->getFile())->resize(400);
			$valueElement = $dom->createElement('value',$image);
			$thumb = Mage::helper('catalog/image')->init($_product, 'image',$_image->getFile())->resize(80);
			
			$thumbElement = $dom->createElement('thumbnail',$thumb);
			$moreViewElement = $dom->createElement('images');
			$moreViewElement->appendChild($valueElement);
			$moreViewElement->appendChild($thumbElement);
			$productDetail->appendChild($moreViewElement);
		}	
		
		$_options = Mage::helper('core')->decorateArray($_product->getOptions());
		if(count($_options)){
			$optionList = $dom->createElement('options');
			foreach($_options as $_option){
				$optionElement = $dom->createElement('option');
				foreach($_option->getData() as $key=>$value){
					$_element = $dom->createElement($key,$value);
					$optionElement->appendChild($_element);
				}
				$values = $_option->getValues();
				$valueList = $dom->createElement('values');
				foreach($values as $_value){
					$valueElement = $dom->createElement('value');
					foreach($_value->getData() as $key=>$value){
						$_element = $dom->createElement($key,$value);
						$valueElement->appendChild($_element);
					}
					$valueList->appendChild($valueElement);
				}
				$optionElement->appendChild($valueList);
				$optionList->appendChild($optionElement);
			}
			$productDetail->appendChild($optionList);
		}
		
		$this->_simplerss->appendChild($productDetail);
		$dom = $this->_element;
		$dom->appendChild($this->_simplerss);
		return $dom->saveXML();		
	}
    public function _construct()
    {
        parent::_construct();
        $this->_init('facebook/export');
    }

	public function getDom(){
		return $this->_element;
	}
	public function initDom(){
		$this->_element = new DOMDocument('1.0','utf-8');
		$this->_element->formatOutput = true;
		$this->_simplerss = $this->_element->createElement('products');
	}
	public function iniStoreXML(){
		$this->_element = new DOMDocument('1.0','utf-8');
		$this->_element->formatOutput = true;
		$this->_simplerss = $this->_element->createElement('stores');	
	}
	public function setPaginatorInfo($totalRecord,$totalPage,$currentPage,$product_limit){
		$this->_totalRecord =$totalRecord;
		$this->_totalPage = $totalPage;
		$this->_currentPage = $currentPage;
		$this->_product_limit = $product_limit;
	}
	protected function addPaginator(){
		$dom = $this->getDom();
		
		$item = $dom->createElement('paginator');
		
		$totalElement = $dom->createElement('total_record',$this->_totalRecord);
		$item->appendChild($totalElement);
		$pageElement = $dom->createElement('total_page',$this->_totalPage);
		$item->appendChild($pageElement);
		$curPageElement = $dom->createElement('current_page',$this->_currentPage);
		$item->appendChild($curPageElement);
		$productLimit = $dom->createElement('product_limit',$this->_product_limit);
		$item->appendChild($productLimit);
		$this->_simplerss->appendChild($item);

	}
	protected  function addToXML($entryData){		
		$dom = $this->getDom();
		$item = $dom->createElement('item');
		foreach($entryData as $key=>$value){
			$valueDom = htmlspecialchars($value);
			$childItem = $dom->createElement($key,$valueDom);
			$item->appendChild($childItem);
		}
		$this->_simplerss->appendChild($item);
	}
	protected function saveXML(){
		$dom = $this->_element;
		$dom->appendChild($this->_simplerss);
		return $dom->saveXML();
	}
	

	public function exportToXML(){
		$this->initDom();
		$_productCollection = $this->getProductCollection();
		
		if (count($_productCollection)>0) {
			foreach ($_productCollection as $_product) {
				$final_price = $_product->getFinalPrice();
				$imagePath ="";
				$baseUrl = Mage::helper('facebook')->refineUrl(Mage::getBaseUrl());
				$baseUrl = str_replace("index.php/","",$baseUrl);
				if($_product->getThumbnail() !=''){
					$imagePath = Mage::helper('catalog/image')->init($_product, 'image')->resize(144);
				}
				$data = array(
							'name'=>htmlspecialchars($_product->getName()),
							'image' =>$imagePath,
							'short_description'=>htmlspecialchars($_product->getShortDescription()),
							'id'=>$_product->getId(),
							'link'  =>$_product->getProductUrl(),
							'price'=>$this->get_product_price($_product)
							);
				$this->addToXML($data);
			}
		}
		if($this->_totalRecord !=-1){
			$this->addPaginator();
		}
        return $this->saveXML();
	}	
	
	public function exportAllProductToXML(){
		$this->initDom();
		$_productCollection = $this->getProductCollection();
		
		if (count($_productCollection)>0) {
			foreach ($_productCollection as $_product) {
				
				$data = array(							
							'id'=>$_product->getId()							
							);
				$this->addToXML($data);
			}
		}
        return $this->saveXML();
	}	
	
	
	public function exportProductToXML(){
		$this->initDom();
		$_productCollection = $this->getProductCollection();
		
		if (count($_productCollection)>0) {
			foreach ($_productCollection as $_product) {
				$final_price = $_product->getFinalPrice();
				$imagePath ="";
				
				if($_product->getThumbnail() !=''){
					$imagePath = Mage::helper('catalog/image')->init($_product, 'image')->resize(200);
				}
				$data = array(
							'name'=>htmlspecialchars($_product->getName()),
							'image' =>$imagePath,
							'short_description'=>htmlspecialchars($_product->getShortDescription()),
							'description'=>htmlspecialchars($_product->getDescription()),
							'id'=>$_product->getId(),
							'link'  =>$_product->getProductUrl(),
							'price'=>$this->get_product_price($_product)
							);
				$this->addToXML($data);
			}
		}
        return $this->saveXML();
	}	
	
	
	function clean_url($text)
	{
		//$text=strtolower($text);
		$code_entities_match = array('®','@');
		$code_entities_replace = array(' ',' ');
		$text = str_replace($code_entities_match, $code_entities_replace, $text);
		return $text;
	} 
	
	function get_product_price($_product)
	{
		$_coreHelper = Mage::helper('core');
	    
	    $_taxHelper  = Mage::helper('tax');
		
		$_finalPrice = $_taxHelper->getPrice($_product, $_product->getFinalPrice());
		
		return $_coreHelper->currency($_finalPrice,true,false);
	}
	public function exportDataToXML($data){
		$this->initDom();
		$this->addToXML($data);
		return $this->saveXML();
	}		
	public function exportAllCategoryToXML(){
		$this->initDom();
		$_categoryCollection = $this->getProductCollection();
		$hide_categories  = Mage::getStoreConfig('facebook/general/hide_categories');
		$hideCategoryIds = array();
		if($hide_categories !=''){
			$hide_categories = explode(',',$hide_categories);
			if(is_array($hide_categories) && count($hide_categories)>0){
				foreach($hide_categories as $key=>$value){
					$category = Mage::getModel('catalog/category')
								->load($value);
					if($category->getId()){
						$hideCategoryIds[] = $category->getId();
					}
				}
			}
		}
		if (count($_categoryCollection)>0) {
			foreach ($_categoryCollection as $_category) {
				if(!empty($hideCategoryIds)){
					if(in_array($_category->getId(),$hideCategoryIds)){
						continue;
					}
					if(in_array($_category->getParentId(),$hideCategoryIds)){
						continue;
					}
				}
				$data = array(
							'name'=>htmlspecialchars($_category->getName()),							
							'id'=>$_category->getEntityId(),
							'link'  =>$_category->getUrl(),
							'parent_id'=>$_category->getParentId(),
							'level'=>$_category->getLevel(),
							'product_count'=>$_category->getProductCount()
						);
				$this->addToXML($data);
			}
		}
        return $this->saveXML();
	}
	public function exportStores($collection){
		$this->iniStoreXML();
		if($collection->getSize()>0){
			foreach($collection as $_store){
				$data = array(
					'store_id' 	 =>$_store->getId(),
					'store_name' =>$_store->getName(),
				);
				$this->addToXML($data);
			}
		
		}
		return $this->saveXML();
	}
}