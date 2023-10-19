<?php

namespace Sunnysideup\HasOneEdit;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DataObjectInterface;
use SilverStripe\ORM\Relation;

/**
 * Class HasOneUploadField
 * @package App\Forms
 */
class HasOneUploadField extends UploadField
{
    /**
     * @var null|bool
     */
    private $hasOneMultiUpload = null;

    /**
     * HasOneUploadField constructor.
     * @param \SilverStripe\AssetAdmin\Forms\UploadField $original
     */
    public function __construct(UploadField $original)
    {
        if (!HasOneEdit::isHasOneEditField($original)) {
            throw new \InvalidArgumentException('Original upload field passed to HasOneUploadField must have the has_one separator "' .
                HasOneEdit::FIELD_SEPARATOR . '" in its name.');
        }

        parent::__construct($original->getName(), $original->title, $original->getItems());

        // Copy state from original upload field
        foreach (get_object_vars($original) as $prop => $value) {
            $this->{$prop} = $value;
        }
    }

    /**
     * Check if allowed to upload more than one file
     * @see \SilverStripe\AssetAdmin\Forms\UploadField::getIsMultiUpload()
     * @return bool
     */
    public function getIsMultiUpload()
    {
        if ($this->hasOneMultiUpload === null) {
            // Guess from record
            list($relationName, $fieldOnRelation) = HasOneEdit::getRelationNameAndField($this);
            $relatedObject = HasOneEdit::getRelationRecord($this->getRecord(), $relationName);

            // Multi-upload disabled for has_one components
            $this->hasOneMultiUpload = !($relatedObject && DataObject::getSchema()->hasOneComponent($relatedObject, $fieldOnRelation));
        }

        return $this->hasOneMultiUpload;
    }

    /**
     * @see \SilverStripe\AssetAdmin\Forms\UploadField::saveInto()
     * @inheritDoc
     */
    public function saveInto(DataObjectInterface $record)
    {
        list($relationName, $fieldOnRelation) = HasOneEdit::getRelationNameAndField($this);
        $record = HasOneEdit::getRelationRecord($this->getRecord(), $relationName);

        // Check type of relation
        $relation = $record->hasMethod($fieldOnRelation) ? $record->$fieldOnRelation() : null;
        if ($relation instanceof Relation) {
            // has_many or many_many
            $relation->setByIDList($this->getItemIDs());
        } elseif ($class = DataObject::getSchema()->hasOneComponent($record, $fieldOnRelation)) {
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
