<?php

namespace Sunnysideup\HasOneEdit;

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
            if (!HasOneEdit::isHasOneEditField($name)) {
                continue;
            }

            list($relationName, $fieldOnRelation) = HasOneEdit::getRelationNameAndField($name);
            $relatedObject = HasOneEdit::getRelationRecord($this->owner, $relationName);
            if ($relatedObject === null) {
                continue;
            }

            $changed = $this->checkIfFieldHasChangeForHasOnedEdit($relatedObject, $fieldOnRelation, $value);

            if ($changed) {
                $toWrite[$relationName] = $relatedObject;
            }
        }

        foreach ($toWrite as $relationName => $obj) {
            $obj->write();
            $this->owner->setField("{$relationName}ID", $obj->ID);
        }
    }

    private function checkIfFieldHasChangeForHasOnedEdit($relatedObject, $fieldOnRelation, $value)
    {
        $relatedObject->setCastedField($fieldOnRelation, $value['after']);
        $dbs = $relatedObject->stat('db');
        $type = $dbs[$fieldOnRelation] ?? '';

        // special case for Enum
        if($type && stripos($type, 'Enum') === 0) {
            $dbFieldObject = $relatedObject->dbObject($fieldOnRelation);
            return $dbFieldObject->getDefault() !== $value['after'];
        } else {
            return ! empty($value['after']) && $relatedObject->isChanged($fieldOnRelation, DataObject::CHANGE_VALUE);
        }
    }


}
