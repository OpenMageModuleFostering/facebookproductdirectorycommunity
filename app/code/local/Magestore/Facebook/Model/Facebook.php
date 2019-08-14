<?php

class Magestore_Facebook_Model_Facebook extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('facebook/facebook');
    }
	
	public function save_config()
	{
		try{
		
			$data = Mage::helper('facebook')->getDataToSend();
							
			$url = 'http://facebook.magestore.com/Receiver.php';
			
			Mage::helper('facebook')->sendDataToUrl($data,$url);
		
		} catch(Exception $e) {
		
		}
	}
}