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
}
