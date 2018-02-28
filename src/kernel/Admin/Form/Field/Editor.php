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
        'height' => 550,
        'codeFold' => true,
        'saveHTMLToTextarea' => true, // 保存 HTML 到 Textarea
        'searchReplace' => true,

    ];

    /**
     *
     * @return \Lxh\Contracts\View\Factory|\Lxh\View\View
     */
    public function render()
    {
        $this->setupScript();

        return parent::render();
    }

    protected function setupScript()
    {
        $server = config('client.resource-server');

        Admin::loadScript($server.'/assets/admin/packages/editor-md/lib/raphael.min.js');
        Admin::loadScript($server.'/assets/admin/packages/editor-md/editormd.min.js');
        Admin::loadScript($server.'/assets/admin/packages/editor-md/languages/en.js');
        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/link-dialog/link-dialog.js');
        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/reference-link-dialog/reference-link-dialog.js');
        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/image-dialog/image-dialog.js');
        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/code-block-dialog/code-block-dialog.js');
        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/table-dialog/table-dialog.js');
        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/emoji-dialog/emoji-dialog.js');
        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/goto-line-dialog/goto-line-dialog.js');
        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/help-dialog/help-dialog.js');
        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/html-entities-dialog/html-entities-dialog.js');
        Admin::loadScript($server.'/assets/admin/packages/editor-md/plugins/preformatted-text-dialog/preformatted-text-dialog.js');

        $id = 'e'.Util::randomString(6);

        $this->attribute('id', $id);

        $this->script = <<<EOF
(function () {
    var c = LXHSTORE.IFRAME.current()
        formg = $('.form-group'),
        ddg = $('.dropdown-btn-group'),
        btg = $('.btn-group');
        
    var editor = editormd("{$id}", {
        height: 740,
        path : '{$server}/assets/admin/packages/editor-md/lib/',

//            markdown : md,
        codeFold : true,
        //syncScrolling : false,
        saveHTMLToTextarea : true,    // 保存 HTML 到 Textarea
        searchReplace : true,
        //watch : false,                // 关闭实时预览
        htmlDecode : "style,script,iframe|on*",            // 开启 HTML 标签解析，为了安全性，默认不开启    
        //toolbar  : false,             //关闭工具栏
        //previewCodeHighlight : false, // 关闭预览 HTML 的代码块高亮，默认开启
        emoji : true,
        taskList : true,
        placeholder: '{$this->getPlaceholder()}',
        name: '{$this->column}',
        tocm            : true,         // Using [TOCM]
        tex : true,                   // 开启科学公式TeX语言支持，默认关闭
        flowChart : true,             // 开启流程图支持，默认关闭
        sequenceDiagram : true,       // 开启时序/序列图支持，默认关闭,
        //dialogLockScreen : false,   // 设置弹出层对话框不锁屏，全局通用，默认为true
        //dialogShowMask : false,     // 设置弹出层对话框显示透明遮罩层，全局通用，默认为true
        //dialogDraggable : false,    // 设置弹出层对话框不可拖动，全局通用，默认为true
        //dialogMaskOpacity : 0.4,    // 设置透明遮罩层的透明度，全局通用，默认值为0.1
        //dialogMaskBgColor : "#000", // 设置透明遮罩层的背景颜色，全局通用，默认为#fff
        imageUpload : true,
        imageFormats : ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
        imageUploadURL : "./php/upload.php",
        onload : function() {
            console.log('onload', this);
            
            LXHSTORE.IFRAME.height(c);
            //this.fullscreen();
            //this.unwatch();
            //this.watch().fullscreen();
            //this.setMarkdown("#PHP");
            //this.width("100%");
            //this.height(480);
            //this.resize("100%", 640);
        },
        onfullscreen : function() {
            formg.hide();
            ddg.hide();
            btg.hide();
            $('#'+this.id).parents('.form-group').show();
            $('.CodeMirror').css({'border-left': '1px solid #ddd'})
        },
        onfullscreenExit : function() {
            formg.show();
            ddg.show();
            btg.show();
            $('.CodeMirror').css({'border-left': '0'})
        }
    });
   
     
})();
             
EOF;

    }
}
