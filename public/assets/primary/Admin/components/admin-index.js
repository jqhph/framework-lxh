/**
 * 管理员详情页js
 */
define(['validate'], function () {
    // 所有js加载完毕时间
    __then__(index);

    function index() {
        $('.roles-list').click(click_role_list_btn)
    }

    var requesting = 0;
    // 点击查看角色列表按钮事件
    function click_role_list_btn(e) {
        if (requesting) return;
        var $this = $(this),
            adminId = $this.data('id'),
            modalId = '#' +$this.data('modal'),
            $modal = $(modalId),
            $loading = loading();

        requesting = 1;
        $this.addClass('disabled');

        $.getJSON('/api/admin/roles-list/' + adminId, function (data) {
            requesting = 0;
            $loading.close();
            $this.removeClass('disabled');
        });

        $modal.modal('show');
    }
});