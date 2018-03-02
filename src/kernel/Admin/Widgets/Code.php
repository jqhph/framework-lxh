<?php

namespace Lxh\Admin\Widgets;

class Code extends Markdown
{
    protected $lang = 'php';

    public function __construct($file = '', $start = 1, $end = 10)
    {
        parent::__construct();

        $file && $this->read($file, $start, $end);
    }

    /**
     * 设置语言
     *
     * @param string $lang
     * @return $this
     */
    public function lang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    public function javascript()
    {
        $this->lang = 'javascript';

        return $this;
    }

    public function html()
    {
        $this->lang = 'html';

        return $this;
    }

    public function java()
    {
        $this->lang = 'java';

        return $this;
    }

    public function python()
    {
        $this->lang = 'python';

        return $this;
    }

    /**
     * 读取指定行上下区间文件内容
     *
     * @param string $file
     * @param int $lineNumber
     * @param int $padding
     * @return $this
     */
    public function padding($file, $lineNumber = 1, $padding = 5)
    {
        return $this->read($file, $lineNumber - $padding, $lineNumber + $padding);
    }

    /**
     * 读取指定行文件内容
     *
     * @param string $file
     * @param int $start
     * @param int $end
     * @return $this
     */
    public function read($file, $start = 1, $end = 10)
    {
        if (!$file or !is_readable($file) || $end < $start) {
            return [];
        }

        $file = fopen($file, 'r');
        $line = 0;

        $source = '';
        while (($row = fgets($file)) !== false) {
            if (++$line > $end)
                break;

            if ($line >= $start) {
                $source .= htmlspecialchars($row, ENT_NOQUOTES, config('charset', 'utf-8'));
            }
        }

        fclose($file);

        return $this->content($source);
    }

    protected function build()
    {
        return <<<EOF
<div {$this->formatAttributes()}><textarea style="display:none;">
```{$this->lang}
{$this->content}
```
</textarea></div>
EOF;

    }
}
