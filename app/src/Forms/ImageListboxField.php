<?php
namespace Streunerkatzen\Forms;

use SilverStripe\Forms\ListboxField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

class ImageListboxField extends ListboxField {
    /**
     * Gets the list of options to render in this formfield
     *
     * @return ArrayList
     */
    public function getOptions() {
        // Loop through and figure out which values were selected.
        $options = [];
        $selectedValue = $this->getValueArray();
        foreach ($this->getSource() as $item) {
            $itemValue = $item->Value;
            $title = $item->Title;

            $itemSelected = in_array($itemValue, $selectedValue)
                || in_array($itemValue, $this->getDefaultItems());
            $itemDisabled = $this->isDisabled()
                || in_array($itemValue, $this->getDisabledItems());
            $options[] = new ArrayData([
                'Title' => $title,
                'Value' => $itemValue,
                'Selected' => $itemSelected,
                'Disabled' => $itemDisabled,
                'Examples' => $item->Examples
            ]);
        }

        $options = new ArrayList($options);
        $this->extend('updateGetOptions', $options);
        return $options;
    }
}
