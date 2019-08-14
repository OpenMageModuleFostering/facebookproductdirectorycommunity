<?php

class Magestore_Facebook_Block_Adminhtml_Facebook_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'facebook';
        $this->_controller = 'adminhtml_facebook';
        
        $this->_updateButton('save', 'label', Mage::helper('facebook')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('facebook')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('facebook_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'facebook_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'facebook_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('facebook_data') && Mage::registry('facebook_data')->getId() ) {
            return Mage::helper('facebook')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('facebook_data')->getTitle()));
        } else {
            return Mage::helper('facebook')->__('Add Item');
        }
    }
}