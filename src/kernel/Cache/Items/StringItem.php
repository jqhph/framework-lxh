<?php

namespace Lxh\Cache\Items;

class StringItem extends Item
{
    protected function normalizeFetchedContent(&$content)
    {
        return $content;
    }

    protected function normalizeSettingContent(&$content)
    {
        return $content;
    }
}
