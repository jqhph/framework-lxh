<?php

namespace Lxh\Admin\Widgets;

use Lxh\Admin\Admin;
use Lxh\Contracts\Support\Renderable;
use Lxh\Helper\Util;

class Code extends Markdown
{
    protected $lang = 'php';

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

    public function render()
    {
        $id = 'm'.Util::randomString(6);

        $this->attribute('id', $id);

        $opts = json_encode($this->options);

        Admin::script("editormd.markdownToHTML('$id', $opts);");

        return <<<EOF
<div {$this->formatAttributes()}><textarea style="display:none;">
```{$this->lang}
{$this->content}
```
</textarea></div>
EOF;

    }
}
