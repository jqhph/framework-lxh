/**
 * 管理员详情页js
 */
define([], function () {
    // 所有js加载完毕时间
    __then__(index);

    function index() {
        $('.roles-list').click(click_role_list_btn)
    }

    var requesting = 0, rolesList = {}, $loading = null, $tag, adminId;
    // 点击查看角色列表按钮事件
    function click_role_list_btn(e) {
        if (requesting) return;
        $tag = $(this);
        adminId = $tag.data('id');
        var modalId = '#' +$tag.data('modal'), $modal = $(modalId);

        $loading = loading();
        requesting = 1;
        $tag.addClass('disabled');

        fetch_roles_list(adminId, function (content) {
            if (content) {
                $modal.find('.modal-body').html(content);
                $modal.modal('show');
            } else {
                $lxh.ui().notify().info(trans('No data.'));
            }
        });
    }

    // 获取角色列表数据
    function fetch_roles_list(adminId, callback) {
        if (typeof rolesList[adminId] != 'undefined') {
            readyclick();
            return callback(rolesList[adminId]);
        }
        $.getJSON('/api/admin/roles-list/' + adminId, function (data) {
            readyclick();
            // 缓存数据到js对象，不用每次都去服务器取
            rolesList[adminId] = data.content;
            callback(rolesList[adminId])
        });
    }

    function readyclick() {
        requesting = 0;
        $loading.close();
        $tag.removeClass('disabled');
    }
});