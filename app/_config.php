<?php

namespace Streunerkatzen;

use SilverStripe\Admin\CMSMenu;
use SilverStripe\CampaignAdmin\CampaignAdmin;
use SilverStripe\Forms\HTMLEditor\TinyMCEConfig;
use SilverStripe\View\Parsers\ShortcodeParser;

ShortcodeParser::get('default')->register('cat', [BlogArticle::class, 'CatShortcode']);
TinyMCEConfig::get('cms')
    ->enablePlugins(['catplugin' => 'plugin/catplugin.js'])
    ->addButtonsToLine(2, 'catplugin');

// remove campaigns menu
CMSMenu::remove_menu_class(CampaignAdmin::class);
