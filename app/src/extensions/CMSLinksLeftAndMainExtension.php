<?php
namespace Streunerkatzen\Extensions;

use SilverStripe\Admin\CMSMenu;
use SilverStripe\Admin\LeftAndMainExtension;
use SilverStripe\Versioned\Versioned;
use Streunerkatzen\Constants;
use Streunerkatzen\Elements\CatElementForm;

class CMSLinksLeftAndMainExtension extends LeftAndMainExtension {
    public function init() {
        $catForms = Versioned::get_by_stage(CatElementForm::class, Versioned::LIVE);

        foreach ($catForms as $catForm) {
            $id = "CatForm_" . $catForm->ID;
            $priority = 1;     // lower number --> lower in the list
            $attributes = [
                'target' => '_self'
            ];

            $title = "Katzenformular";
            $link = "";

            $submissionsToReview = $catForm->Submissions()->filter([
                'CatFormSubmissionStatus' => [Constants::CAT_STATUS_NEW, Constants::CAT_STATUS_EDITED],
            ]);

            if ($submissionsToReview->count() > 0) {
                $title .= " (" . $submissionsToReview->count() . ")";
            }

            $link = $catForm->CMSEditLink() . "#Root_Submissions";


            CMSMenu::add_link($id, $title, $link, $priority, $attributes);
        }
    }
}
