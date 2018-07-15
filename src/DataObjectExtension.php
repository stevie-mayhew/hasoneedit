<?php

namespace SGN\HasOneEdit;

use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;

class DataObjectExtension extends DataExtension
{
    /**
     * @see \SilverStripe\ORM\DataObject::onBeforeWrite()
     */
    public function onBeforeWrite()
    {
        $changed = $this->owner->getChangedFields();
        $toWrite = [];

        foreach ($changed as $name => $value) {
            if (!HasOneEdit::isHasOneEditField($name)) continue;

            list($relationName, $fieldOnRelation) = HasOneEdit::getRelationNameAndField($name);
            $relatedObject = HasOneEdit::getRelationRecord($this->owner, $relationName);
            if ($relatedObject === null) continue;

            $relatedObject->setCastedField($fieldOnRelation, $value['after']);
            if ($relatedObject->isChanged(null, DataObject::CHANGE_VALUE)) {
                $toWrite[$relationName] = $relatedObject;
            }
        }

        foreach ($toWrite as $relationName => $obj) {
            $obj->write();
            $this->owner->setField("{$relationName}ID", $obj->ID);
        }
    }
}
