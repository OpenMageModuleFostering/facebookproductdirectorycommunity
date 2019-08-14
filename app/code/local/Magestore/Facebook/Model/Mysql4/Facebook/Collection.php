<?php

class Magestore_Facebook_Model_Mysql4_Facebook_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('facebook/facebook');
    }
}