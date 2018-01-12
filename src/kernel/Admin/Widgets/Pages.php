<?php

namespace Lxh\Admin\Widgets;

class Pages
{
    /**
     * 起始行数
     *
     * @var int
     */
    public $firstRow;

    /**
     * 列表每页显示行数
     *
     * @var int
     */
    public $listRows;

    /**
     * 页数跳转时要带的参数
     *
     * @var string
     */
    public $parameter;

    /**
     * 分页总页面数
     *
     * @var int
     */
    protected $totalPages;

    /**
     * 总行数
     *
     * @var int
     */
    protected $totalRows;

    /**
     * 当前页数
     *
     * @var int
     */
    protected $currentPage = 1;

    /**
     * 分页的栏的总页数
     *
     * @var int
     */
    protected $coolPages;

    /**
     * 分页栏每页显示的页数
     *
     * @var int
     */
    protected $rollPage;

    /**
     * 分页显示定制
     *
     * @var array
     */
    protected $config;

    /**
     * 分页跳转url
     *
     * @var string
     */
    protected $url;

    /**
     * 分页栏每页显示的页数的中间数
     *
     * @var int
     */
    protected $centerNum;

    public function __construct()
    {
        $this->config = [
            'totalText' => trans_with_global('Total'),
            'showingText' => trans_with_global('Showing'),
            'prev' => '<',
            'next' => '>',
            'first' => '<<',
            'last' => '>>',
            'ele' => 'a',
            'itemEle' => '<li class="paginate_button">',
            'activeItemEle' => '<li class="paginate_button active">',
            'itemEleEnd' => '</li>',
            'class' => '',
            'currentClass' => 'active',
            'pagekey' => 'page',
            'theme' => '%header%%pageinfo%%first%%upPage%%linkPage%%downPage%%end%'
        ];
    }

    /**
     * @param int $totalRows 总行数
     * @param int $listRows 列表每页显示的行数
     * @param string $parameter url参数
     * @param int $rollPage 分页栏每页显示的页数
     * */
    public function make($totalRows, $listRows = 10, $parameter = '', $rollPage = 10)
    {
        $this->totalRows = $totalRows;
        $this->parameter = $parameter;
        $this->rollPage = $rollPage;
        $this->listRows = $listRows;
        $this->totalPages = ceil($this->totalRows / $this->listRows);     //总页数
        $this->coolPages = ceil($this->totalPages / $this->rollPage);
        $this->currentPage = !empty($_GET['page']) ? intval($_GET['page']) : 1;

        if ($this->currentPage > $this->totalPages) {
            $this->currentPage = $this->totalPages;
        }

        if ($this->currentPage < 1) {
            $this->currentPage = 1;
        }

        $this->firstRow = $this->listRows * ($this->currentPage - 1);

        // 计算分页栏每页显示的页数的中间数
        $temp = $this->rollPage % 2;

        if ($temp == 0) {
            $this->centerNum = $this->rollPage / 2;
        } else {
            $this->centerNum = ($this->rollPage - 1) / 2 + 1;
        }

        return $this->show();
    }

    // 获取当前页数
    public function current()
    {
        return $this->currentPage;
    }

    // 设置配置参数
    public function set($name, $value)
    {
        if (isset($this->config[$name])) {
            $this->config[$name] = &$value;
        }
        return $this;
    }

    public function url()
    {
        if ($this->url) {
            return $this->url;
        }
        $p = $this->config['pagekey'];

        $url = $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], '?') === false ? '?' : '') . $this->parameter;
        $parse = parse_url($url);
        if (isset($parse['query'])) {
            parse_str($parse['query'], $params);
            unset($params[$p]);
            $url = $parse['path'] . '?' . http_build_query($params);

        }
        return $this->url = &$url;
    }

    public function show()
    {
        if (0 == $this->totalRows) return '';

        $pageStr = str_replace(
            array(
                '%header%',
                '%pageinfo%',
                '%first%',
                '%upPage%',
                '%linkPage%',
                '%downPage%',
                '%end%'
            ),
            array(
                $this->header(),
                $this->pageinfo(),
                $this->first(),
                $this->prev(),
                $this->pageList(),
                $this->next(),
                $this->last()
            ),
            $this->config['theme']);
        return $pageStr;
    }

    public function header()
    {
        return "{$this->config['itemEle']}<span class='{$this->config['class']}'>{$this->config['totalText']} {$this->totalRows} / {$this->config['showingText']} {$this->listRows}</span>{$this->config['itemEleEnd']}";
    }

    public function pageinfo()
    {
        return "{$this->config['itemEle']}<span class='{$this->config['class']}'>{$this->currentPage}/{$this->totalPages}</span>{$this->config['itemEleEnd']}";
    }

    // 1 2 3 4 5
    public function pageList()
    {
        $p = $this->config['pagekey'];

        $url = $this->url();

        $linkPage = '';

        for ($i = 1; $i <= $this->rollPage; $i++) {
            $_ = $this->currentPage - $this->centerNum;

            if ($_ <= 0) {
                $_ = 0;
            } elseif ($this->currentPage + $this->centerNum >= $this->totalPages) {
                $_ = $this->totalPages - $this->rollPage;
                $_ = $_ < 0 ? 0 : $_;
            }
            $page = $_ + $i;

            if ($page != $this->currentPage) {
                if ($page <= $this->totalPages) {
                    $linkPage .= "{$this->config['itemEle']}<{$this->config['ele']} class='{$this->config['class']}' 
                    href='$url&$p=$page' data-page='$page'> $page </{$this->config['ele']}>{$this->config['itemEleEnd']}";
                } else {
                    break;
                }
            } else {
                if ($this->totalPages != 1) {
                    $linkPage .=
                        "{$this->config['activeItemEle']}<span class='{$this->config['class']}'>$page</span>{$this->config['itemEleEnd']}";
                }
            }
        }

        return $linkPage;
    }

    public function prev()
    {
        $p = $this->config['pagekey'];

        $url = $this->url();

        //上下翻页字符串
        $upRow = $this->currentPage - 1;

        if ($upRow > 0) {
            $upPage = "{$this->config['itemEle']}<{$this->config['ele']} class='{$this->config['class']}' 
                    data-page='$upRow' href='$url&$p=$upRow'>{$this->config['prev']}</{$this->config['ele']}>{$this->config['itemEleEnd']}";
        } else {
            $upPage = '';
        }
        return $upPage;
    }

    public function next()
    {
        $p = $this->config['pagekey'];

        $url = $this->url();

        //上下翻页字符串
        $downRow = $this->currentPage + 1;
        if ($downRow <= $this->totalPages) {
            $downPage = "{$this->config['itemEle']}<{$this->config['ele']} class='{$this->config['class']}'  data-page='$downRow' 
                        href='$url&$p=$downRow'>{$this->config['next']}</{$this->config['ele']}>{$this->config['itemEleEnd']}";
        } else {
            $downPage = '';
        }
        return $downPage;
    }

    public function first()
    {
        $p = $this->config['pagekey'];

        $url = $this->url();

        // << < > >>
        if ($this->currentPage - $this->centerNum <= 0) {
            $theFirst = '';
        } else {
            $theFirst = "{$this->config['itemEle']}<{$this->config['ele']} class='{$this->config['class']}'  data-page='1' href='$url&$p=1' >
                    {$this->config['first']}</{$this->config['ele']}>{$this->config['itemEleEnd']}";
        }
        return $theFirst;
    }

    public function last()
    {
        $p = $this->config['pagekey'];

        $url = $this->url();

        if ($this->centerNum + $this->currentPage >= $this->totalPages) {
            $nextPage = '';
            $theEnd = '';
        } else {
            $theEnd = "{$this->config['itemEle']}<{$this->config['ele']} class='{$this->config['class']}'  data-page='{$this->totalPages}' href='$url&$p={$this->totalPages}' >
            {$this->config['last']}</{$this->config['ele']}>{$this->config['itemEle']}";
        }
        return $theEnd;
    }

}
