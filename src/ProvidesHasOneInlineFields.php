<?php

namespace SGN\HasOneEdit;

/**
 * Trait ProvidesHasOneInlineFields
 * @package SGN\HasOneEdit
 * @mixin \SilverStripe\ORM\DataObject
 */
trait ProvidesHasOneInlineFields
{
    /**
     * @param string $relationName
     * @return \SilverStripe\Forms\FieldList|\SilverStripe\Forms\FormField[]
     */
    public function provideHasOneInlineFields($relationName)
    {
        $fields = $this->getCMSFields()->dataFields();

        foreach ($fields as $name => $field) {
            $field->setName($relationName . HasOneEdit::FIELD_SEPARATOR . $field->getName());
        }

        return $fields;
    }
}
