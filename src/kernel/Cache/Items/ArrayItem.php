<?php

namespace Lxh\Cache\Items;

class ArrayItem extends Item
{
    protected function normalizeFetchedContent(&$content)
    {
        return json_decode($content, true);
    }

    protected function normalizeSettingContent(&$content)
    {
        return json_encode($content);
    }
}
