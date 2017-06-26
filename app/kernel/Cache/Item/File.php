<?php
/**
 * File item
 *
 * @author Jqh
 * @date   2017/6/16 18:40
 */

namespace Lxh\Cache\Item;

use Lxh\Cache\Item;

class File extends Item
{
    public function __construct($key, $content)
    {
        
        parent::__construct($key, $content);
    }
}
