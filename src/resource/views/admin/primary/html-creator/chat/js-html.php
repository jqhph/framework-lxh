<?php
/**
 * Js模板
 *
 * @author Jqh
 * @date   2017/12/19 11:20
 */

// 访客列表模板 class active选中效果
echo <<<EOF
<script type="text/html" id="visitor-list-tpl">
<li class="{class}" data-id="{id}"><a title="置顶" class="top-tag "></a>
<a title="结束对话" class="close"><i class="zmdi zmdi-close"></i></a><div class="fa fa-user-secret "></div>
<div class="info"><h4>{name}<span class="msg-num">{msgNum}</span></h4></div>
</li>
</script>
EOF;

// 访客对话窗
echo <<<EOF
<script type="text/html" id="visitor-dialog-tpl">
<div class="ember-view" data-id="{id}">
    <div class="chat-item">
        <div class="chat-left">
            <div class="chat-item-info">
                <span class="name">&nbsp;{name}&nbsp;</span> -
                <time>&nbsp;{date}&nbsp;</time>
            </div>
            <div class="chat-info">{msg}</div>
        </div>
    </div>
</div>
</script>
EOF;

// 客服对话窗
echo <<<EOF
<script type="text/html" id="user-dialog-tpl">
<div  class="ember-view">
    <div class="chat-item">
        <div class="chat-right">
            <div class="chat-item-info">
                <span class="name">&nbsp;{username}&nbsp;</span> -
                <time>&nbsp;{date}&nbsp;</time>
            </div>
            <div class="chat-info">{msg}</div>
        </div>
    </div>
</div>
</script>
EOF;

// 图片消息
echo <<<EOF
<script type="text/html" id="img-msg-tpl">
<img src="{src}"  class="f-preview-img">
</script>
EOF;

// 聊天窗容器，包括对话窗，发送表单，按钮等等
echo <<<EOF
<script type="text/html" id="chat-container-tpl">
    <div class="kchat-im-panel-main-chat list1">
        <div  class="kchat-im-panel-main-chat-content scrollbar-auto scrollbar-auto ember-view">
            <div class="chat-box chat-style1 f-preview-context"><div  class="ember-view"></div></div>
        </div>
        <div class="kchat-im-panel-main-chat-textarea ember-view">
            <div class="top-bar ember-view"><ul>
                    <li tabindex="0" id="emoji-face-btn" class="ember-view"><i class="f-toggle-btn icon-chat1"></i></li>
                    <li class="ember-view" style="display: none"><i class="icon-chat2"></i>
                        <form><input type="file" id="attachmentHolder" name="" value="" placeholder="" multiple="multiple" hidden="hidden"></form>
                    </li>
                </ul>
            </div>
            <textarea placeholder="输入信息…" class="js-message-content ember-text-area ember-view"></textarea>
            <div class="bottom-bar">
                <ul class="left ember-view"><li class="switching-user"><a>转接客服</a></li></ul>
                <div class="send"><a>发送</a>
                    <div tabindex="0" class="ember-view" style="display: inline-block;">
                        <span><i class="icon-expand-less"></i></span>
                        <style type="text/css">.opened .send-pop {display: block !important;}</style>
                        <div class="send-pop kchat-pop" style="display:none"><ul><li>欢迎语</li><li>结束语</li></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</script>
EOF;

// tab按钮模板  class active选中效果
echo <<<EOF
<script type="text/html" id="title-tab-tpl">
    <a class="{class}">{label}</a>
</script>
EOF;

// 已设置的标签列表
echo <<<EOF
<script type="text/html" id="tag-list-tpl">
@foreach {taglist} {k} {row}
<dd><span class="status status2">{row.name}<a data-action="remove-tag" tag-id="{row.id}">×</a></span></dd>
@endforeach
</script>
EOF;

echo <<<EOF
<script type="text/html" id="title-tab-tpl">
    <div class="kchat-im-panel-main-info ember-view">
        <div class="scrollbar-auto">
            <div class="chat-user-info ember-view"><div id="ember2176" class="ember-view">
                    <dl class="ember-view"><dt>访客信息</dt>
                        <dd class="f-organization-select">
                            <span class="s-field-label">昵称：</span>
                            <input value="{nickname}" name="nickname" type="text" placeholder="昵称" class="ember-text-field ember-view">
                        </dd>
                        <dd class="f-organization-select">
                            <span class="s-field-label">备注姓名：</span>
                            <input value="{name}" name="name" type="text" placeholder="备注姓名" class="ember-text-field ember-view">
                        </dd>
                        <dd class="f-organization-select">
                            <span class="s-field-label">邮箱：</span>
                            <input value="email" name="enail" type="text" placeholder="邮箱" class="ember-text-field ember-view">
                        </dd>
                        <dd class="f-organization-select">
                            <span class="s-field-label">手机：</span>
                            <input value="phone" name="phone" type="text" placeholder="手机" class="ember-text-field ember-view">
                        </dd>
                        <dd>
                            <a data-id="{visitorid}" class="btn btn-sm" data-action="save-visitor-info">保存</a>
                        </dd>
                    </dl>
                    </div>
                <dl>
                    <dt>访问信息</dt>
                    <dd>对话ID: {sessionid}</dd>
                    <dd>持续时间：<span class="usetime">{date}</span></dd>
                    <dd>人工服务时长：{serviceTime}</dd>
                    <dd>首次响应时长：{firstUsetime}</dd>
                </dl>
                <dl>
                    @foreach otherInfo row
                    <dt>{row}</dt>
                    @endforeach
                </dl>
                <dl class="kf5-chat-tag ember-view"><dt>标签</dt>
                    <dd class="add-tag cb">
                        <div tabindex="0" class="ember-view">
                            <input type="text" placeholder="添加标签" style="display: none;" class="text drop-btn response-text-field ember-text-field ">
                            <a data-action="add-tag" class="dropup  btn btn-sm green btn-hollow ember-view">添加标签</a>
                            <div  style="display: none;" class="drop-box drop-select-box   ember-view">    <ul>
                                <li><a data-action="select-tag" data-id="0" class="drop-opt f-drop-opt-0 "><span class="status status2">产品</span></a></li>
                                <li><a data-action="select-tag" data-id="1" class="drop-opt f-drop-opt-1 "><span class="status status2">咨询</span></a></li>
                                <li><a data-action="select-tag" data-id="2" class="drop-opt f-drop-opt-2 "><span class="status status2">售后</span></a></li>
                                <li><a data-action="select-tag" data-id="3" class="drop-opt f-drop-opt-3"><span class="status status2">投诉</span></a></li>
                            </ul>
                            </div></div></dd>
                </dl>
            </div>
            <div class="prohibition"><p style="display:none"><a>封禁用户</a></p></div>
        </div>
    </div>
</script>
EOF;


?>

<script type="text/html" id="title-tab-tpl">

</script>