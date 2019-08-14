<?php

class Magestore_Facebook_Block_Adminhtml_Facebook_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('facebook_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('facebook')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('facebook')->__('Item Information'),
          'title'     => Mage::helper('facebook')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('facebook/adminhtml_facebook_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}