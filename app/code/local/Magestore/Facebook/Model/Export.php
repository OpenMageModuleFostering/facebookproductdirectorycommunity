<?php

class Magestore_Facebook_Model_Export extends Mage_Core_Model_Abstract
{
	protected $_element ;
	protected $_simplerss;
	protected $_productCollection;
	protected $_product;
	
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
	public function exportTotalProduct($total){
		$this->initDom();
		$parentElement = $this->getDom();
		$totalProductElement = $parentElement->createElement('total',$total);
		$parentElement->appendChild($totalProductElement);
		$this->_simplerss->appendChild($parentElement);
		$dom = $this->_element;
		return $dom->saveXML();			
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
					$imagePath = Mage::helper('catalog/image')->init($_product, 'image')->resize(70);
				}
				$data = array(
							'name'=>$this->clean_url($_product->getName()),
							'image' =>$imagePath,
							'short_description'=>$this->clean_url($_product->getShortDescription()),
							'id'=>$_product->getId(),
							'link'  =>$_product->getProductUrl(),
							'price'=>$this->get_product_price($_product)
							);
				$this->addToXML($data);
			}
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
							'name'=>$this->clean_url($_product->getName()),
							'image' =>$imagePath,
							'short_description'=>$this->clean_url($_product->getShortDescription()),
							'description'=>$this->clean_url($_product->getDescription()),
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
		$text=strtolower($text);
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
	
	public function exportAllCategoryToXML(){
		$this->initDom();
		$_categoryCollection = $this->getProductCollection();
		
		if (count($_categoryCollection)>0) {
			foreach ($_categoryCollection as $_category) {
				//print_r($_category);die();				
				$data = array(
							'name'=>$this->clean_url($_category->getName()),							
							'id'=>$_category->getEntityId(),
							'link'  =>$_category->getUrl(),
							'product_count'=>$_category->getProductCount()
							);
				$this->addToXML($data);
			}
		}
        return $this->saveXML();
	}
}