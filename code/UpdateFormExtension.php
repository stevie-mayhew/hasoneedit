<?php

namespace SGN\HasOneEdit;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\Form;

class UpdateFormExtension extends Extension
{
	/**
	 * @param Form $form
	 */
	public function updateEditForm(Form $form)
	{
		$record = $form->getRecord();
		$fields = $form->Fields()->dataFields();
		
		foreach ($fields as $name => $field) {
			$name = str_replace(array(':', '/'), DataObjectExtension::SEPARATOR, $name);

			if (!strpos($name, DataObjectExtension::SEPARATOR)) {
				// Also skip $name that starts with a separator
				continue;
			}

			$field->setName($name);

			if (!$record) {
				continue;
			}

			if ($field->Value()) {
				// Skip fields that already have a value
				continue;
			}

			list($hasOne, $key) = explode(DataObjectExtension::SEPARATOR, $name, 2);

			if ($record->hasOne($hasOne)) {
				$rel = $record->getComponent($hasOne);
				// Copied from loadDataFrom()
				$exists = (
					isset($rel->$key) ||
					$rel->hasMethod($key) ||
					($rel->hasMethod('hasField') && $rel->hasField($key))
				);

				if ($exists) {
					$value = $rel->__get($key);
					$field->setValue($value);
				}
			}
		}
	}

	/**
	 * @param Form $form
	 */
	public function updateItemEditForm(Form $form) {
		$this->updateEditForm($form);
	}
}
