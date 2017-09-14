<?php

namespace Lxh\View;

use Lxh\Helper\Util;

class ViewName
{
    /**
     * Normalize the given event name.
     *
     * @param  string  $name
     * @return string
     */
    public static function normalize(& $name)
    {
//        $delimiter = ViewFinderInterface::HINT_PATH_DELIMITER;

        return Util::convertWith(str_replace('/', '.', $name), true, '-');
//        if (strpos($name, $delimiter) === false) {
//
//        }
//
//        list($namespace, $name) = explode($delimiter, $name);
//
//        return $namespace.$delimiter.str_replace('/', '.', $name);
    }
}
