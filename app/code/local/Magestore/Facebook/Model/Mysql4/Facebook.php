<?php

class Magestore_Facebook_Model_Mysql4_Facebook extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the facebook_id refers to the key field in your database table.
        $this->_init('facebook/facebook', 'facebook_id');
    }
}