define(['validate'], function () {
    // 所有js加载完毕时间
    __then__(detail);

    function detail() {
        var v = $lxh.validator(window.formRules || [], submit);

        var model = $lxh.createModel();
        var notify = $lxh.ui().notify();

        function submit(e) {
            notify.remove();
            var $loading;

            // 设置请求开始回调函数
            model.on('start', function (api, method, data) {
                $loading = window.loading();
            });

            // 设置成功回调函数
            model.on('success', function (data) {
                // success
                $loading.close();
                notify.success(trans('success'));
                // 500豪秒后跳转到菜单编辑界面
                close_tab(name)
            });
            model.on('any', function () {
                $loading.close();
            });

            // 发起修改或新增操作
            model.save()
        }
    }
});