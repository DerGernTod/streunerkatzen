<?php
namespace Streunerkatzen\Extensions;

use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\DataExtension;
use SilverStripe\UserForms\Model\EditableFormField;

class EditableFormFieldExtension extends DataExtension {
    /**
     * Get the list of classes that can be selected and used as data-values
     *
     * @param $includeLiterals Set to false to exclude non-data fields
     * @return array
     */
    public function getEditableCatFieldClasses($includeLiterals = true) {
        $classes = ClassInfo::getValidSubClasses(EditableFormField::class);

        // Remove classes we don't want to display in the dropdown.
        $editableFieldClasses = [];
        foreach ($classes as $class) {
            // Skip abstract / hidden classes
            if (Config::inst()->get($class, 'abstract', Config::UNINHERITED)
                || Config::inst()->get($class, 'hidden')
            ) {
                continue;
            }

            if (!$includeLiterals && Config::inst()->get($class, 'literal')) {
                continue;
            }

            $singleton = singleton($class);
            if (!$singleton->canCreate()) {
                continue;
            }

            $editableFieldClasses[$class] = $singleton->i18n_singular_name();
        }

        asort($editableFieldClasses);
        return $editableFieldClasses;
    }

    public function getEditableStandardFieldClasses($includeLiterals = true) {
        $classes = ClassInfo::getValidSubClasses(EditableFormField::class);

        // Remove classes we don't want to display in the dropdown.
        $editableFieldClasses = [];
        foreach ($classes as $class) {
            // Skip abstract / hidden classes
            if (Config::inst()->get($class, 'abstract', Config::UNINHERITED)
                || Config::inst()->get($class, 'hidden')
            ) {
                continue;
            }

            if (!$includeLiterals && Config::inst()->get($class, 'literal')) {
                continue;
            }

            // Skip cat data form fields for normal forms as they are not needed and just clutter the form field selection
            if (Config::inst()->get($class, 'is_cat_field')) {
                continue;
            }

            $singleton = singleton($class);
            if (!$singleton->canCreate()) {
                continue;
            }

            $editableFieldClasses[$class] = $singleton->i18n_singular_name();
        }

        asort($editableFieldClasses);
        return $editableFieldClasses;
    }
}
