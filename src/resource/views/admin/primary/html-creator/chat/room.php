<style type="text/css">
    #kchat-im-panel {margin-left: auto;left: auto;right: 50px;}
    .f-organization-select {position: relative;overflow: visible!important;max-width: 320px; }
    .s-field-label { display: inline-block;width: 60px; }
    .f-organization-select .select { display: inline-block; width: 160px; vertical-align: middle; }
    .f-organization-select .select a { color: inherit; }
    .kf5-chat-tag .add-tag ul li a { color: #0096C4; padding: 5px 10px; }
</style>
<div  class="ember-view">
    <div id="kchat-im-panel" class="kchat-group-chat ember-view ui-draggable" style="display: none">
        <header id="ember1329" class="kchat-im-panel-header ember-view"><div class="kchat-alert warning">
                <!-- 目前有warning & success两种通知状态， js控制添加.show出现，效果点『转接客服』查看-->
                <p></p>
            </div>
            <div class="kchat-im-panel-header-user ember-view"><div class="drag-handle js-drag-handle ui-draggable-handle"></div>
                <img src="<?php echo $heads;?>" alt="">
                <div class="info">
                    <h4><?php echo $username?></h4>
                    <div class="state ember-view">
                        <a class="<?php echo $status?>"><?php echo $statusName?></a>
                        <div class="state-choice" style="display: none">
                            <ul><li class="online" >在线</li><li class="busyline" >忙碌</li><li class="offline" >离线</li></ul>
                        </div>
                    </div>

                </div>
            </div>

            <div class="kchat-im-panel-header-operation">
                <div class="drag-handle js-drag-handle ui-draggable-handle"></div>
                <div  class="right-top-btn ember-view">
                    <a title="关闭窗口" class="close close-chat-box" ><i class="zmdi zmdi-close"></i></a>
                </div>
                <nav class="nav1 ember-view"></nav>
                <nav class="nav2 ember-view"></nav>
            </div>
        </header>

        <div class="kchat-im-panel-body">
            <div class="kchat-im-panel-body-left ember-view"><div class="kchat-im-panel-userlist-nav ember-view">
                    <a class=" active ember-view">对话中</a>
                    <a class="ember-view">已结束</a>
                </div>
                <div class="kchat-im-panel-userlist scrollbar-auto">
                    <div class="kchat-talk-list-group">
                        <div class="kchat-talk-list-empty" style="">
                            <p>暂无进行中对话</p>
                        </div>
                        <div class="ember-view">
                            <ul class="visitor-list"></ul>
                        </div>
                    </div>
                </div>

            </div>

            <div class="kchat-im-panel-main">
               <?php // 聊天窗主要内容 ?>
                <div class="kchat-talk-list-empty ember-view"><i class="icon-talk1"></i>
                    <p>
                        暂无对话<br>
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
echo load_css('admin/im.min');
echo load_js('draggabilly');
echo load_js('chat/im');
?>

<script>
(function (w) {

    var $impanel = $('#kchat-im-panel')

    $impanel.draggabilly();

    $('.notification-box .right-bar-toggle').click(function () {
        $impanel.toggle(100)
    })
    $impanel.find('.close-chat-box').click(function () {
        $impanel.hide(100)
    })

})(window)
</script>
