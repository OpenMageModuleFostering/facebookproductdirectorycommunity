<?php
class Magestore_Facebook_Block_Facebook extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getFacebook()     
     { 
        if (!$this->hasData('facebook')) {
            $this->setData('facebook', Mage::registry('facebook'));
        }
        return $this->getData('facebook');
        
    }
}