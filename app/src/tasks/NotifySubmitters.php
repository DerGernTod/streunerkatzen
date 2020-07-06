<?php
namespace Streunerkatzen;

use SilverStripe\Control\Director;
use SilverStripe\Dev\BuildTask;

class NotifySubmitters extends BuildTask {
    public function run($request) {
        $triggerNotifiers = Notifier::get()
        ->filter(['NextReminder:LessThanOrEqual' => date('Y-m-d H:i:s')]);
        echo ($triggerNotifiers->count())." Katzen zum Benachrichtigen gefunden.";
        $notifiersPerEmail = [];
        foreach($triggerNotifiers as $notifier) {
            $contact = $notifier->Cat->Contact;
            if (!filter_var($contact, FILTER_VALIDATE_EMAIL)) {
                echo "Person hat keine Emailadresse hinterlassen: $contact";
            } else {
                $nextReminder = date('Y-m-d H:i:s', strtotime("+2 week"));
                $notifier->NextReminder = $nextReminder;
                $notifier->write();

                if (!isset($notifiersPerEmail[$contact])) {
                    $notifiersPerEmail[$contact] = [];
                }
                $notifiersPerEmail[$contact][] = $notifier;
            }
        }

        foreach($notifiersPerEmail as $email => $notifiers) {
            EmailHelper::sendCatEntryReminderMail($notifiers, $email);
        }
    }
}
