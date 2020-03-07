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
                echo "Person hat keine Emailadresse hinterlassen: $contact";
            } else {
                $nextReminder = date('Y-m-d H:i:s', strtotime("+2 week"));
                $notifier->NextReminder = $nextReminder;
                $notifier->write();
                // TODO: send email here
                echo "TODO: send email to $contact ".Director::absoluteBaseURL();
            }
        }
        if ($triggerNotifiers->count()) {
            echo 'Keine Erinnerungen zu senden.';
        }
    }
}
