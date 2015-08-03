<?php

class sgn_hasoneedit_UpdateFormExtension extends \Extension {
	public function updateEditForm(\Form $form) {
		$record = $form->getRecord();
		$fields = $form->Fields()->dataFields();
		
		foreach($fields as $name => $field) {
			$name = str_replace(array(':', '/'), sgn_hasoneedit_DataObjectExtension::separator, $name);
			if(!strpos($name, sgn_hasoneedit_DataObjectExtension::separator)) {
				// Also skip $name that starts with a separator
				continue;
			}
			$field->setName($name);
			if(!$record) {
				continue;
			}
			if($field->Value()) {
				// Skip fields that already have a value
				continue;
			}
			list($hasone, $key) = explode(sgn_hasoneedit_DataObjectExtension::separator, $name, 2);
			if($record->has_one($hasone)) {
				$rel = $record->getComponent($hasone);
				// Copied from loadDataFrom()
				$exists = (
					isset($rel->$key) ||
					$rel->hasMethod($key) ||
					($rel->hasMethod('hasField') && $rel->hasField($key))
				);

				if($exists) {
					$value = $rel->__get($key);
					$field->setValue($value);
				}
			}
		}
	}

	public function updateItemEditForm(\Form $form) {
		$this->updateEditForm($form);
	}
}
