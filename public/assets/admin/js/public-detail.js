(function () {
    var completed = 0, current = LXHSTORE.IFRAME.current();
    $(document).on('app.completed', function () {
        completed = 1;
    });

    new LxhLoader(['@lxh/js/validate.min'], function () {
        // 所有js加载完毕时间
        if (completed) {
            detail();
        } else {
            $(document).on('app.completed', detail);
        }
        function detail() {
            var v = $lxh.validator(window.formRules || [], submit);
            var model = $lxh.createModel(),
                notify = $lxh.ui().notify(),
                n = NProgress;
            function submit(e) {
                notify.remove();

                // 设置请求开始回调函数
                model.on('start', function (api, method, data) {
                    n.start();
                });

                // 设置成功回调函数
                model.on('success', function (data) {
                    // success
                    n.done();
                    notify.success(trans('success'));
                    if (!model.getId()) {
                        // 创建记录成功后关闭当前页面
                        close_tab(current);
                    }
                });
                model.on('any', function () {
                    n.done();
                });

                // 发起修改或新增操作
                model.save()
            }
        }
    }).request();
})();