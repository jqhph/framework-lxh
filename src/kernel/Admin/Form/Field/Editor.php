<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Admin;
use Lxh\Admin\Form\Field;
use Lxh\Helper\Util;

class Editor extends Field
{
    /**
     * 编辑器样式
     *
     * @var array
     */
    protected static $css = [
        '@lxh/packages/editor-md/css/editormd.min'
    ];

    /**
     *
     * @var string
     */
    protected $view = 'admin::form.editor';

    /**
     * 编辑器配置
     *
     * @var array
     */
    protected $options = [
        'height' => 600,
        'codeFold' => true,
        'saveHTMLToTextarea' => true, // 保存 HTML 到 Textarea
        'searchReplace' => true,
//        'htmlDecode' => 'style,script,iframe|on*', // 开启 HTML 标签解析，为了安全性，默认不开启
        'emoji' => true,
        'taskList' => true,
        'tocm' => true,         // Using [TOCM]
        'tex' => true,                   // 开启科学公式TeX语言支持，默认关闭
        'flowChart' => 'true',             // 开启流程图支持，默认关闭
        'sequenceDiagram' => true,       // 开启时序/序列图支持，默认关闭,
        'imageUpload' => true,
        'imageFormats' => ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'webp'],
        'imageUploadURL' => '',
    ];

    /**
     *
     * @return \Lxh\Contracts\View\Factory|\Lxh\View\View
     */
    public function render()
    {
        $this->setupScript();

        $this->prepend('<i class="fa fa-edit"></i>');

        return parent::render();
    }

    /**
     * 开启 HTML 标签解析，为了安全性，默认不开启
     * style,script,iframe|on*
     *
     * @param string $decode
     * @return $this
     */
    public function htmlDecode($decode)
    {
        $this->options['htmlDecode'] = &$decode;

        return $this;
    }

    /**
     * 设置编辑器容器高度
     *
     * @param int $height
     * @return $this
     */
    public function height($height)
    {
        $this->options['height'] = $height;

        return $this;
    }

    protected function setupScript()
    {
        $server = config('client.resource-server');

        Admin::loadScript($server.'/assets/admin/packages/editor-md/lib/raphael.min.js');
        Admin::loadScript($server.'/assets/admin/packages/editor-md/editormd.min.js');
        Admin::loadScript($server.'/assets/admin/packages/editor-md/languages/en.js');

        $id = 'e'.Util::randomString(6);

        $this->attribute('id', $id);

        $this->options['path'] = $server.'/assets/admin/packages/editor-md/lib/';
        $this->options['name'] = $this->column;
        $this->options['placeholder'] = $this->getPlaceholder();

        $opts = json_encode($this->options);

        $this->script = <<<EOF
(function () {
    var c = LXHSTORE.IFRAME.current()
        formg = $('.form-group'),
        ddg = $('.dropdown-btn-group'),
        btg = $('.btn-group'),
        card = $('.card'), 
        row = $('.row'),
        opts = {$opts};
        
    opts.onload = function() {
        console.log('editor onload', this);   
        LXHSTORE.IFRAME.height(c);
    };
    opts.onfullscreen = function() {
        formg.hide();
        ddg.hide();
        btg.hide();
        card.hide();
        row.hide();
        var t = $('#'+this.id);
        t.parents('.card').show()
        t.parents('.form-group').show();
        t.parents('.row').show();
        $('.CodeMirror').css({'border-left': '1px solid #ddd'})
    };
    opts.onfullscreenExit = function() {
        formg.show();
        ddg.show();
        btg.show();
        card.show();
        row.show();
        $('.CodeMirror').css({'border-left': '0'})
    };
    editormd("{$id}", opts);
})();       
EOF;

    }

//        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/link-dialog/link-dialog.js');
//        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/reference-link-dialog/reference-link-dialog.js');
//        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/image-dialog/image-dialog.js');
//        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/code-block-dialog/code-block-dialog.js');
//        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/table-dialog/table-dialog.js');
//        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/goto-line-dialog/goto-line-dialog.js');
//        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/preformatted-text-dialog/preformatted-text-dialog.js');
//        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/emoji-dialog/emoji-dialog.js');
//        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/help-dialog/help-dialog.js');
//        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/html-entities-dialog/html-entities-dialog.js');

}
