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
    public function provideHasOneInlineFields(string $relationName, ?bool $dbFieldsOnly = false)
    {
        $fields = $this->getCMSFields()->dataFields();
        $restrictions = [];
        if ($dbFieldsOnly) {
            $restrictions = $this->Config()->get('db');
        }
        foreach ($fields as $name => $field) {
            if (empty($restrictions) || in_array($name, $restrictions)) {
                $field->setName($relationName . HasOneEdit::FIELD_SEPARATOR . $field->getName());
            }
        }

        return $fields;
    }
}
