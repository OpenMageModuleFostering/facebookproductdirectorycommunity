<?php

class Magestore_Facebook_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getDataToSend()
	{
		$data = Mage::getStoreConfig('facebook/general');
		
		$data['base_url'] = Mage::getBaseUrl();
		
		return $data;
	}
	public function getTotalProduct(){
		
	}
	public function sendDataToUrl($data,$url)
	{
		try{
			$data_string = '';
			
			foreach($data as $key=>$value) 
			{ 
				$data_string .= $key.'='.$value.'&version=0.1.2&'; 
			}
			$data_string .= 'version=0.1.2&app_type=magento&';
			rtrim($data_string,'&');
		
			$ch = curl_init();
			
			curl_setopt($ch,CURLOPT_URL,$url);
			
			curl_setopt($ch,CURLOPT_POST,count($data));
			
			curl_setopt($ch,CURLOPT_POSTFIELDS,$data_string);		
			
			$result = curl_exec($ch);
			
			curl_close($ch);
		
		} catch(Exception $e) {
		
		}				
	}
	
	public function refineUrl($url)
	{
		$url = str_replace('http://','',$url);
		$url = str_replace('www.','',$url);
		$index = strpos($url,'index.php');
		$url = substr($url,0,$index);
		$url = preg_replace  ( "/\/(.*)\//"  , ""  , $url  );
		$url = str_replace('/','',$url);
		
		
		return $url;
	}

}