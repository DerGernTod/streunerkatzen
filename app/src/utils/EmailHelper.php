<?php
namespace Streunerkatzen\Utils;

use Exception;
use SilverStripe\Control\Email\Email;
use SilverStripe\ORM\ArrayList;

class EmailHelper {
    public static function sendReviewMail($tokenUrl, $catName, $reviewTemplate, $reviewMessage, $address) {

        $tokenLink = "<a href='$tokenUrl'>$tokenUrl</a>";

        $template = str_replace('$CatName', $catName, $reviewTemplate);
        $template = str_replace('$ReviewComment', $reviewMessage, $template);
        $template = str_replace('$EntryLink', $tokenLink, $template);

        $email = new Email('noreply@streunerkatzen.org', $address, 'Überarbeite Deinen Eintrag', $template);
        echo "$address<br />$template";
        $result = $email->send();
        if (!$result) {
            throw new Exception("Error sending email.");
        }
    }

    public static function sendSearchAgentNotificationMail($email, $cat, $tokenUrl) {

    }

    public static function sendAgentRegisteredMail() {

    }

    /**
     * @param Notifier[] $notifiers
     * @param string $address
     */
    public static function sendCatEntryReminderMail($notifiers, $address) {
        $template = (new ArrayList($notifiers))->renderWith("Streunerkatzen/Includes/Mail/CatEntryReminderMail");
        $email = new Email('admin@gernotraudner.at', $address, 'Überarbeite Deinen Eintrag', $template);
        $result = $email->send();
        if (!$result) {
            throw new Exception("Error sending email.");
        }
    }
}
