<?php
namespace Streunerkatzen;

use Exception;
use SilverStripe\Control\Email\Email;

class EmailHelper {
    // TODO nothing here seems to work, but probably due to my local mailserver not accepting the request
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
}
