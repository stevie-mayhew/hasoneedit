<?php

namespace SGN\HasOneEdit;
use SilverStripe\ORM\DataObject;

/**
 * Class HasOneEdit
 * @package SGN\HasOneEdit
 */
class HasOneEdit
{
    /**
     *
     */
    const FIELD_SEPARATOR = '-_1_-';

    /**
     *
     */
    const SUPPORTED_SEPARATORS = [
        self::FIELD_SEPARATOR,
        ':',
        '/',
    ];

    /**
     * @param \SilverStripe\Forms\FormField|string $field
     * @return string[] Array of [relation name, field on relation]
     */
    public static function getRelationNameAndField($field)
    {
        if (!is_string($field)) {
            $field = $field->getName();
        }

        return explode(static::FIELD_SEPARATOR, $field, 2);
    }

    /**
     * @param \SilverStripe\ORM\DataObject $parent
     * @param string $relationName
     * @return \SilverStripe\ORM\DataObject|null
     */
    public static function getRelationRecord(DataObject $parent, $relationName)
    {
        return array_key_exists($relationName, $parent->hasOne()) || array_key_exists($relationName, $parent->belongsTo(false))
            ? $parent->getComponent($relationName)
            : null;
    }

    /**
     * @param \SilverStripe\Forms\FormField|string $field
     * @return bool
     */
    public static function isHasOneEditField($field)
    {
        if (!is_string($field)) {
            $field = $field->getName();
        }

        return boolval(strpos($field, static::FIELD_SEPARATOR));
    }

    /**
     * @param string $fieldName
     * @return string
     */
    public static function normaliseSeparator($fieldName)
    {
        return str_replace(static::SUPPORTED_SEPARATORS, static::FIELD_SEPARATOR, $fieldName);
    }

    /**
     * @param \SilverStripe\ORM\DataObject $parent
     * @param string $relation
     * @return \SilverStripe\Forms\FieldList|\SilverStripe\Forms\FormField[]
     */
    public static function getInlineFields(DataObject $parent, $relation)
    {
        /** @var \SilverStripe\ORM\DataObject|\SGN\HasOneEdit\ProvidesHasOneInlineFields $relatedObject */
        $relatedObject = static::getRelationRecord($parent, $relation);
        return $relatedObject->provideHasOneInlineFields($relation);
    }
}
