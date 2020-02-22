<?php
namespace Streunerkatzen;

use SilverStripe\Dev\Debug;
use SilverStripe\ORM\DataExtension;
use SilverStripe\UserForms\Model\Submission\SubmittedForm;
use SilverStripe\UserForms\Model\Submission\SubmittedFormField;
use SilverStripe\View\Requirements;

class UserDefinedFormControllerExtension extends DataExtension {
    public function onAfterInit() {
        Requirements::themedJavascript("form.js");
    }
}
