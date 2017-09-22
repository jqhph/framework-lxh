<?php
/**
 *
 * @author {author}
 * @date   {date}
 */

namespace Lxh\Admin\Controller;

use Lxh\Exceptions\NotFound;
use Lxh\Helper\Console;
use Lxh\Helper\Util;
use Lxh\MVC\Controller;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Status;
use Lxh\Task\BEADS\ProdDetail;

class Test extends Controller
{
    public function actionTest(Request $req, Response $resp, & $params)
    {

    }



    public function actionHello()
    {
        
    }


    protected function table($data, array $titles = [])
    {
        if (! $titles) {
            $titles = array_keys($data[0]);
        }

        $table = '<table border="1" style="margin-left:10px;border-collapse: collapse;text-align:center;"><tr>';
        foreach ($titles as &$title) {
            $table .= "<th>$title</th>";
        }
        $table .= "</tr>";

        foreach ($data as & $row) {
            $table .= '<tr>';
            foreach ($titles as & $title) {
                $table .= "<td>{$row[$title]}</td>";
            }
            $table .= '</tr>';
        }
        return $table . '</table>';
    }
}
