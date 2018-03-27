<?php

namespace Lxh\Admin\Widgets;

use Lxh\Admin\Admin;
use Lxh\Contracts\Support\Renderable;
use Lxh\Helper\Util;

class Markdown extends Widget
{
    protected static $loadedAssets = false;

    /**
     * markdown内容
     *
     * @var string
     */
    protected $content;

    /**
     * 配置
     *
     * @var array
     */
    protected $options = [
        'htmlDecode' => 'style,script,iframe',
        'emoji' => true,
        'taskList' => true,
        'tex' => true,
        'flowChart' => true,
        'sequenceDiagram' => true,
    ];

    public function __construct($markdown = '')
    {
        $this->content($markdown);

        if (! static::$loadedAssets) {
            static::$loadedAssets = true;

            Admin::style('code,pre{font-family:Consolas,"Liberation Mono",Menlo,Courier,monospace;word-wrap:break-word}.com{color:#93A1A1}.lit{color:#6cf}.opn{color:#93A1A1}.clo{color:#93A1A1}.tag{color:#6cf}.atn{color:#00bfbf}.atv{color:#fc8bb3}.dec{color:teal}.var{color:teal}.fun{color:#DC322F}pre.prettyprint{padding:10px;border:1px solid #E1E1E8;tab-size:4}pre.prettyprint.linenums{box-shadow:40px 0 0 #FBFBFC inset,41px 0 0 #ECECF0 inset}pre.prettyprint.linenums ol.linenums{color:#1E347B;padding-left:30px!important;margin-top:0;margin-bottom:0}pre.prettyprint.linenums ol.linenums li{color:#BEBEC5;line-height:18px;padding-left:12px!important;background:#333!important}pre.prettyprint.linenums ol.linenums li.L0,pre.prettyprint.linenums ol.linenums li.L1,pre.prettyprint.linenums ol.linenums li.L2,pre.prettyprint.linenums ol.linenums li.L3,pre.prettyprint.linenums ol.linenums li.L4,pre.prettyprint.linenums ol.linenums li.L5,pre.prettyprint.linenums ol.linenums li.L6,pre.prettyprint.linenums ol.linenums li.L7,pre.prettyprint.linenums ol.linenums li.L8,pre.prettyprint.linenums ol.linenums li.L9{list-style-type:decimal!important}.typ{color:#8abeb7}pre.prettyprint{background:#333!important;width:100%;border:0}.pln{color:#DEDEDD}.kwd{color:#d7a6d7}.pun{color:#fff}.str{color:#e6db74}pre.prettyprint.linenums{box-shadow:40px 0 0 #333 inset,41px 0 0 #444 inset}pre.prettyprint{background-color:#333!important;border:0 solid #333}');

            Admin::loadStyle('/assets/admin/packages/editor-md/css/editormd.preview.min.css');

            Admin::loadScript([
                '/assets/admin/packages/editor-md/lib/raphael.min.js',
                '/assets/admin/packages/editor-md/lib/marked.min.js',
                '/assets/admin/packages/editor-md/lib/prettify.min.js',
                '/assets/admin/packages/editor-md/lib/underscore.min.js',
                '/assets/admin/packages/editor-md/lib/sequence-diagram.min.js',
                '/assets/admin/packages/editor-md/lib/flowchart.min.js',
                '/assets/admin/packages/editor-md/lib/jquery.flowchart.min.js',
                '/assets/admin/packages/editor-md/editormd.min.js'
            ]);
        }
    }

    public function option($k, $v)
    {
        $this->options[$k] = $v;

        return $this;
    }

    /**
     *
     * @param string $markdown
     * @return $this
     */
    public function content($markdown)
    {
        $this->content = &$markdown;
        return $this;
    }

    protected function build()
    {
        if ($this->content instanceof Renderable) {
            $this->content = $this->content->render();
        }

        return <<<EOF
<div {$this->formatAttributes()}><textarea style="display:none;">{$this->content}</textarea></div>
EOF;

    }

    public function render()
    {
        $id = 'm'.Util::randomString(6);

        $this->attribute('id', $id);

        $opts = json_encode($this->options);

        Admin::script("editormd.markdownToHTML('$id', $opts);");

        return $this->build();
    }

}
