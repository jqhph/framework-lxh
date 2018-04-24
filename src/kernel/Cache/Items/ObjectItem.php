<?php

namespace Lxh\Cache\Items;

class ObjectItem extends Item
{
    protected function normalizeFetchedContent(&$content)
    {
        return json_decode($content);
    }

    protected function normalizeSettingContent(&$content)
    {
        return json_encode($content);
    }
}
