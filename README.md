# Has One Edit

This module allows you to directly edit the fields of a related `has_one` object directly, without having to mess around with `GridField` or links to `ModelAdmin`. If the related `has_one` doesn't exist yet, then this module also creates the object and sets up the relation for you on first write.

This module has been tested editing a `has_one` in both a `GridFieldDetailForm` and on a generic `Page` in `CMSMain`.

## Requirements

SilverStripe 4.x.

Basic testing has been carried out on 4.0.x-dev. Extensive testing may be required as SS4 becomes more solid.

## Usage

To use this module, simply add a field to the CMS fields for your object in your `getCMSFields()` method. The name of the field should be `HasOneName-_1_-FieldName`.

For example, say you have a `has_one` called `Show` and that `has_one` has a field called `Title` you want to edit. You'd add the field `TextField::create('Show-_1_-Title', 'Show Title')`.

If you do not require that the outputted name of the field matches the value you supply, you can also use a colon as a separator instead of `-_1_-`.

### Generating fields with the `ProvidesHasOneInlineFields` trait

If you simply want to display all the CMS fields for a related object, you can add the `ProvidesHasOneInlineFields` trait to the object. This adds a method which calls `getCMSFields()`
on your `DataObject` and return the `FormField`s for that object. Those `FormField`s will be converted for use with this module by adding the relation name and separator to their name.

In the owning object, where you want to display the fields, call `HasOneEdit::getInlineFields($this, 'my_has_one_name', ['db'])`. This will return the `db` subset of fields for adding to the CMS - e.g. you
can display the related object's fields in their own tab by calling `$fields->addFieldsToTab('Root.RelatedObject', HasOneEdit::getInlineFields($this, 'Relation', ['db']))`.

This has the advantage of running the entire `getCMSFields()` call tree (e.g. `updateCMSFields` for any functionality provided via extension) etc. without having to repeat logic
in a lot of places.

You can also implement a method `public function provideHasOneInlineFields($relationName)` returning `FieldList|FormField[]` to provide a custom interface different
to `getCMSFields()` (e.g. just a small subset of fields). In this case, all the field names should be in the form `$relationName . HasOneEdit::FIELD_SEPARATOR . $dataObjectFieldName`.
This method will be called by `HasOneEdit::getInlineFields` even if your class does not use the `ProvidesHasOneInlineFields` trait.

### Using with your own form

To add support to your own forms, you need to add the `Sunnysideup\HasOneEdit\UpdateFormExtension` extension to your controller and call `$this->extend('updateEditForm', $form)` before returning the form to the template. Without this, the fields will not get populated with the values from the `has_one` though saving will work.
