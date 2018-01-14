<?php

namespace Lxh\Language;

class I18n
{
    public function __construct()
    {
    }

    public function gettext($string, $context = null)
    {
        if ($context === null) {
            return _($string);
        }

        $contextString = "{$context}\004{$string}";
        $translation = _($contextString);

        return $translation == $contextString ? $string : $translation;
    }
}
