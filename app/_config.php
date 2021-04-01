<?php

namespace Streunerkatzen;

use SilverStripe\Admin\CMSMenu;
use SilverStripe\CampaignAdmin\CampaignAdmin;
use SilverStripe\Forms\HTMLEditor\TinyMCEConfig;
use SilverStripe\Security\PasswordValidator;
use SilverStripe\Security\Member;
use SilverStripe\i18n\i18n;
use SilverStripe\View\Parsers\ShortcodeParser;
use Streunerkatzen\Cats\Cat;

// remove PasswordValidator for SilverStripe 5.0
$validator = PasswordValidator::create();
// Settings are registered via Injector configuration - see passwords.yml in framework
Member::set_password_validator($validator);

i18n::set_locale('de_DE');
i18n::config()
    ->set('date_format', 'dd.MM.yyyy')
    ->set('time_format', 'HH:mm');

ShortcodeParser::get('default')->register('cat', [Cat::class, 'CatShortcode']);
TinyMCEConfig::get('cms')
    ->enablePlugins(['catplugin' => 'plugin/catplugin.js'])
    ->addButtonsToLine(2, 'catplugin');

// remove campaigns menu
CMSMenu::remove_menu_class(CampaignAdmin::class);
