<?php
namespace Streunerkatzen\Tasks;

use Exception;
use SilverStripe\Dev\BuildTask;
use Streunerkatzen\Cats\Notifier;
use Streunerkatzen\Utils\EmailHelper;

class NotifySubmitters extends BuildTask {
    public function run($request) {
        $triggerNotifiers = Notifier::get()
        ->filter(['NextReminder:LessThanOrEqual' => date('Y-m-d H:i:s')]);
        echo ($triggerNotifiers->count())." Katzen zum Benachrichtigen gefunden.";
        $notifiersPerEmail = [];
        foreach ($triggerNotifiers as $notifier) {
            $contact = $notifier->Cat->Contact;
            if (!filter_var($contact, FILTER_VALIDATE_EMAIL)) {
                echo "Person hat keine Emailadresse hinterlassen: $contact";
            } else {
                $nextReminder = date('Y-m-d H:i:s', strtotime("+2 week"));
                $notifier->NextReminder = $nextReminder;

                if (!isset($notifiersPerEmail[$contact])) {
                    $notifiersPerEmail[$contact] = [];
                }
                $notifiersPerEmail[$contact][] = $notifier;
            }
        }

        foreach ($notifiersPerEmail as $email => $notifiers) {
            try {
                EmailHelper::sendCatEntryReminderMail($notifiers, $email);
                foreach ($notifiers as $notifier) {
                    $notifier->write();
                }
            } catch (Exception $e) {
                echo "Fehler beim Senden: $e";
            }
        }
    }
}
