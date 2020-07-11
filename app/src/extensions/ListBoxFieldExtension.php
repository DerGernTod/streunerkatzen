<?php
namespace Streunerkatzen;

use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\ORM\DataExtension;

class ListboxFieldExtension extends DataExtension {
    public function getOptionsWithExamples() {
        // Loop through and figure out which values were selected.
        $options = array();
        $selectedValue = $this->owner->getValueArray();
        foreach ($this->owner->getSource() as $item) {
            $value = $item->Value;
            $title = $item->Title;
            $itemSelected = in_array($value, $selectedValue)
                || in_array($value, $this->owner->getDefaultItems());
            $itemDisabled = $this->owner->isDisabled()
                || in_array($value, $this->owner->getDisabledItems());
            $options[] = new ArrayData(array(
                'Title' => $title,
                'Value' => $value,
                'Selected' => $itemSelected,
                'Disabled' => $itemDisabled,
                'Examples' => $item->Examples
            ));

        }

        $options = new ArrayList($options);
        $this->owner->extend('updateGetOptions', $options);
        return $options;
    }


    /**
     * Validate this field
     *
     * @param Validator $validator
     * @return bool
     */
    public function validate($validator)
    {
        $values = $this->owner->getValueArray();
        $validValues = $this->owner->getValidValues();

        // Filter out selected values not in the data source
        $self = $this;
        $invalidValues = array_filter(
            $values,
            function ($userValue) use ($self, $validValues) {
                foreach ($validValues as $formValue) {
                    if ($self->owner->isSelectedValue($formValue, $userValue)) {
                        return false;
                    }
                }
                return true;
            }
        );
        if (empty($invalidValues)) {
            return true;
        }

        // List invalid items
        $validator->owner->validationError(
            $this->owner->getName(),
            "Validierungsfehler: valide werte: ".join(", ", $validValues)." <br />gegebene werte: ".join(", ", $values)
            /*_t(
                'SilverStripe\\Forms\\MultiSelectField.SOURCE_VALIDATION',
                "Please select values within the list provided. Invalid option(s) {value} given",
                array('value' => implode(',', $invalidValues))
            )*/,
            "validation"
        );
        return false;
    }
}
