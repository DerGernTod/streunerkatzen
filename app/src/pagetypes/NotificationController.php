<?php
namespace Streunerkatzen;

use PageController;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\View\Requirements;

class NotificationController extends PageController {
    private static $allowed_actions = [
        "configure",
        "unsubscribe",
        "timespan",
        "delete"
    ];

    protected function init() {
        parent::init();
        Requirements::themedJavascript("notifier.js");
    }

    public function unsubscribe(HTTPRequest $request) {
        $token = $this->owner->getRequest()->postVar('token');
        $notifier = Notifier::get()->filter(["EditToken" => $token])->first();
        if (!$notifier) {
            return $this->httpError(404, 'Ungültiger Link. Möglicherweise wurde diese Benachrichtigung bereits gelöscht.');
        }
        $notifier->delete();
        return 'success';
    }

    public function timespan(HTTPRequest $request) {
        $token = $this->owner->getRequest()->postVar('token');
        $notifier = Notifier::get()->filter(["EditToken" => $token])->first();
        $weeks = $this->owner->getRequest()->postVar('weeks');
        if (!$notifier || !$weeks) {
            return $this->httpError(404, 'Ungültiger Link. Möglicherweise wurde diese Benachrichtigung bereits gelöscht.');
        }

        $notifier->NextReminder = date('Y-m-d H:i:s', strtotime("+$weeks week"));
        $notifier->write();
        return 'success';
    }

    public function delete(HTTPRequest $request) {
        $token = $this->owner->getRequest()->postVar('token');
        $notifier = Notifier::get()->filter(["EditToken" => $token])->first();
        if (!$notifier) {
            return $this->httpError(404, 'Ungültiger Link. Möglicherweise wurde diese Benachrichtigung bereits gelöscht.');
        }
        $notifier->Cat->delete();
        $notifier->delete();
        return 'success';
    }

    public function configure(HTTPRequest $request) {
        $token = $this->owner->getRequest()->getVar('token');
        $notifier = Notifier::get()->filter(["EditToken" => $token])->first();
        if (!$notifier) {
            return $this->httpError(404, 'Ungültiger Link. Möglicherweise wurde diese Benachrichtigung bereits gelöscht.');
        }

        return $this->customise([
            'Content' => $notifier->renderWith('Streunerkatzen/Includes/NotificationConfig')
        ])->renderWith('Page');
    }
}
?>
