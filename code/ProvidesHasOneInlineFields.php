<?php

/**
 * Trait ProvidesHasOneInlineFields
 * @mixin DataObject
 */
trait ProvidesHasOneInlineFields
{
    /**
     * @param string $relationName
     * @return FieldList|FormField[]
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
