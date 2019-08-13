<?php
class AroundIo_MageExt_Block_MageExt extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getMageExt()     
     { 
        if (!$this->hasData('mageext')) {
            $this->setData('mageext', Mage::registry('mageext'));
        }
        return $this->getData('mageext');
        
    }
}