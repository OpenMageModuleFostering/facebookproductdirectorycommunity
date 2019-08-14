<?php

class Magestore_Facebook_Block_Adminhtml_Facebook_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('facebook_form', array('legend'=>Mage::helper('facebook')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('facebook')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('facebook')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('facebook')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('facebook')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('facebook')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('facebook')->__('Content'),
          'title'     => Mage::helper('facebook')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getFacebookData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getFacebookData());
          Mage::getSingleton('adminhtml/session')->setFacebookData(null);
      } elseif ( Mage::registry('facebook_data') ) {
          $form->setValues(Mage::registry('facebook_data')->getData());
      }
      return parent::_prepareForm();
  }
}