new LxhLoader(['@lxh/js/validate.min'], function () {
    // 所有js加载完毕时间
    $(document).on('app.completed', detail);

    function detail() {
        var v = $lxh.validator(window.formRules || [], submit);
        var model = $lxh.createModel(),
            notify = $lxh.ui().notify(),
            name = LXHSTORE.IFRAME.current(),
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
            });
            model.on('any', function () {
                n.done();
            });

            // 发起修改或新增操作
            model.save()
        }
    }
}).request();