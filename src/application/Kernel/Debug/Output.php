<?php

namespace Lxh\Debug;

class Output
{
    /**
     * 以table格式输出数组
     *
     * @param array $data
     * @param array $titles
     * @return string 
     */
    public static function table(array $data, array $titles = [])
    {
        if (count($data) < 0) {
            return '';
        }

        if (! $titles) {
            $titles = array_keys($data[0]);
        }

        $table = '<table border="1" style="margin-left:10px;border-collapse: collapse;text-align:center;"><tr>';
        foreach ($titles as &$title) {
            $table .= "<th style='padding:10px;font-size:14px;'>$title</th>";
        }
        $table .= "</tr>";

        foreach ($data as & $row) {
            $table .= '<tr>';
            foreach ($titles as & $title) {
                $table .= "<td style='padding:10px;font-size:13px;'>{$row[$title]}</td>";
            }
            $table .= '</tr>';
        }
        return $table . '</table>';
    }


}
