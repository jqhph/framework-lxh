/**
 * 使用ajax加载数据并用bootstrap的modal弹窗展示
 */
define(['blade'], function () {
    // 所有js加载完毕时间
    __then__(index);

    var clickEl = window.ajaxModalEl || '.ajax-modal';

    function index() {
        $(clickEl).click(show_modal_btn)
    }

    var requesting = 0, contents = {}, $loading = null, $tag, id, $modal;
    // 点击查看角色列表按钮事件
    function show_modal_btn(e) {
        if (requesting) return;
        $tag = $(this);
        id = $tag.data('id');
        var title = $tag.attr('modal-title'), url = $tag.attr('modal-url');

        $loading = loading();
        requesting = 1;
        $tag.addClass('disabled');

        fetch_data(id, url, function (content) {
            if (content) {
                if (! $modal) {
                    $modal = $lxh.ui().modal({
                        title: title, confirmBtn: false
                    });
                }

                $modal.find('.modal-body').html(content);
                $modal.modal('show');
            } else {
                $lxh.ui().notify().info(trans('No data.'));
            }
        });
    }

    // 获取服务器数据
    function fetch_data(id, url, callback) {
        if (typeof contents[id] != 'undefined') {
            readyclick();
            return callback(contents[id]);
        }
        $.getJSON(url, function (data) {
            readyclick();
            // 缓存数据到js对象，不用每次都去服务器取
            contents[id] = data.content;
            callback(contents[id])
        });
    }

    function readyclick() {
        requesting = 0;
        $loading.close();
        $tag.removeClass('disabled');
    }
});