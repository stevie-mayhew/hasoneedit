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
     * @param array $fieldsToShow
     *               If nothing is provided, all getCMSFields dataFields will show.
     *               You can set fields to show by providing
     *               db, has_one, many_many, etc. OR
     *               You can also set the names of actual fields - e.g. MyTitle, Notes, etc.
     * @return \SilverStripe\Forms\FieldList|\SilverStripe\Forms\FormField[]
     */
    public function provideHasOneInlineFields(string $relationName, ?array $fieldsToShow = [])
    {
        // work out which fields to show
        $finalFieldsToShow = [];
        if (count($fieldsToShow) > 0) {
            foreach ($fieldsToShow as $fieldsToShowEntry) {
                $relList = array_keys($this->Config()->get($fieldsToShowEntry));
                $list = is_array($relList) ? $relList : [$fieldsToShowEntry];
                $finalFieldsToShow = array_merge(
                    $finalFieldsToShow,
                    $list
                );
            }
        }

        // compile the form fields
        $fields = $this->getCMSFields()->dataFields();
        foreach ($fields as $name => $field) {
            if (count($finalFieldsToShow) === 0 || in_array($name, $finalFieldsToShow)) {
                $field->setName($relationName . HasOneEdit::FIELD_SEPARATOR . $field->getName());
            } else {
                unset($fields[$name]);
            }
        }

        return $fields;
    }
}
