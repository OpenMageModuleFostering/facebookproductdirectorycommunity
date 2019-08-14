<?php
class Magestore_Facebook_Block_Adminhtml_Facebook extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_facebook';
    $this->_blockGroup = 'facebook';
    $this->_headerText = Mage::helper('facebook')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('facebook')->__('Add Item');
    parent::__construct();
  }
}