<?php

/**
 * Class HasOneUploadField
 */
class HasOneUploadField extends UploadField
{

    /**
     * HasOneUploadField constructor.
     * @param UploadField $original
     */
    public function __construct(UploadField $original)
    {
        if (!HasOneEdit::isHasOneEditField($original)) {
            throw new InvalidArgumentException('Original upload field passed to HasOneUploadField must have the has_one separator "' .
                HasOneEdit::FIELD_SEPARATOR . '" in its name.');
        }

        parent::__construct($original->getName(), $original->title, $original->getItems());

        // Copy state from original upload field
        foreach (get_object_vars($original) as $prop => $value) {
            $this->{$prop} = $value;
        }
    }

    /**
     * @see UploadField::saveInto()
     * @inheritDoc
     */
    public function saveInto(DataObjectInterface $record)
    {
        list($relationName, $fieldOnRelation) = HasOneEdit::getRelationNameAndField($this);
        $record = HasOneEdit::getRelationRecord($this->getRecord(), $relationName);

        // Check type of relation
        $relation = $record->hasMethod($fieldOnRelation) ? $record->$fieldOnRelation() : null;
        if ($relation && ($relation instanceof RelationList || $relation instanceof UnsavedRelationList)) {
            // has_many or many_many
            $relation->setByIDList($this->getItemIDs());
        } else if ($class = $record->hasOneComponent($fieldOnRelation)) {
            // Get details to save
            $idList = $this->getItemIDs();

            // Assign has_one ID
            $id = !empty($idList) ? reset($idList) : 0;
            $record->setField("{$fieldOnRelation}ID", $id);

            // Polymorphic asignment
            if ($class === DataObject::class) {
                $file = $id ? File::get()->byID($id) : null;
                $fileClass = $file ? get_class($file) : File::class;
                $record->{"{$fieldOnRelation}Class"} = $id ? $fileClass : null;
            }

            // Write has one record
            $record->write();
        }

        return $this;
    }
}
