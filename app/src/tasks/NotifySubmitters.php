<?php
namespace Streunerkatzen;

use SilverStripe\Control\Director;
use SilverStripe\Dev\BuildTask;

class NotifySubmitters extends BuildTask {
    public function run($request) {
        $triggerNotifiers = Notifier::get()
            ->filter(['NextReminder:LessThanOrEqual' => date('Y-m-d H:i:s')]);
        foreach($triggerNotifiers as $notifier) {
            $contact = $notifier->Cat->Contact;
            if (!filter_var($contact, FILTER_VALIDATE_EMAIL)) {
                echo "person didn't use email address as contact reminder: $contact";
            } else {
                echo "TODO: send email to $contact ".Director::absoluteBaseURL();
            }
        }

    }
}
