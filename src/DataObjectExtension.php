<?php

namespace SGN\HasOneEdit;

use SilverStripe\ORM\DataExtension;

class DataObjectExtension extends DataExtension
{
	/**
	 * @var string
	 */
	const SEPARATOR = '-_1_-';

	/**
	 * @see {@link SilverStripe\ORM\DataObject->onBeforeWrite()}
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
			list($hasone, $key) = explode(self::SEPARATOR, $name, 2);
			if($this->owner->has_one($hasone) || $this->owner->belongs_to($hasone)) {
				$rel = $this->owner->getComponent($hasone);

				// Get original:
				$original = (string) $rel->__get($key);

				if ($original !== $value) {
					$rel->setCastedField($key, $value);
					$toWrite[$hasone] = $rel;
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
