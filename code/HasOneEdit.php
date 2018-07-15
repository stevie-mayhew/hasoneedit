<?php

/**
 * Class HasOneEdit
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
     * @param FormField|string $fieldName
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
     * @param DataObject $parent
     * @param string $relationName
     * @return DataObject|null
     */
    public static function getRelationRecord(DataObject $parent, $relationName)
    {
        return ($parent->hasOne($relationName) || $parent->belongsTo($relationName))
            ? $parent->getComponent($relationName)
            : null;
    }

    /**
     * @param FormField|string $field
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
}
