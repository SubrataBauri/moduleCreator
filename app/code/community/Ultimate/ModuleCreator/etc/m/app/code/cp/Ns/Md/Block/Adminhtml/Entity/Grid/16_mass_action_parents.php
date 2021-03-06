		$values = Mage::getResourceModel('{{module}}/{{sibling}}_collection')->toOptionHash();
		$values = array_reverse($values, true); 
		$values[''] = ''; 
		$values = array_reverse($values, true);
		$this->getMassactionBlock()->addItem('{{sibling}}_id', array(
			'label'=> Mage::helper('{{module}}')->__('Change {{SiblingLabel}}'),
			'url'  => $this->getUrl('*/*/mass{{Sibling}}Id', array('_current'=>true)),
			'additional' => array(
				'flag_{{sibling}}_id' => array(
						'name' => 'flag_{{sibling}}_id',
						'type' => 'select',
						'class' => 'required-entry',
						'label' => Mage::helper('{{module}}')->__('{{SiblingLabel}}'),
						'values' => $values
				)
			)
		));
