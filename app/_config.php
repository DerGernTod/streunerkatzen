<?php

namespace Streunerkatzen;
use SilverStripe\View\Parsers\ShortcodeParser;

ShortcodeParser::get('default')->register('cat', ['Streunerkatzen\BlogArticle', 'CatShortcode']);
