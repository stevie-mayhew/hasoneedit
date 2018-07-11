<?php

class sgn_hasoneedit_DataObjectExtension extends DataExtension {

	/**
	 * @var string
	 */
	const SEPARATOR = '-_1_-';

	/**
	 * @see {@link DataObject->onBeforeWrite()}
	 */
	public function onBeforeWrite()
	{
		$changed = $this->owner->getChangedFields();
		$toWrite = array();

		foreach ($changed as $name => $value) {

			if (!strpos($name, self::SEPARATOR)) {
				// Also skip $name that starts with a separator
				continue;
			}

			$value = (string)$value['after'];
			list($hasOne, $key) = explode(self::SEPARATOR, $name, 2);
			if($this->owner->hasOne($hasOne) || $this->owner->belongsTo($hasOne)) {
				$rel = $this->owner->getComponent($hasOne);

				// Get original:
				$original = (string) $rel->{$key};

				if ($original !== $value) {
					$rel->setCastedField($key, $value);
					$toWrite[$hasOne] = $rel;
				}
			}
		}

		foreach ($toWrite as $rel => $obj) {
			$obj->write();

			$key = $rel . 'ID';

			if (!$this->owner->$key) {
				$this->owner->$key = $obj->ID;
			}
		}
	}
}
