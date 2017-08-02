add_action(function () {
    var v = $lxh.validator([
        {name: 'name', rules: 'required|length_between[2-30]'},
    ], submit)

    var model = $lxh.createModel()
    var notify = $lxh.ui().notify()

    function submit(e) {
        notify.remove()
        notify.info(trans('loading'))

        // 设置成功回调函数
        model.on('success', function (data) {
            // success
            notify.remove()
            notify.success(trans('success'))

            // 500豪秒后跳转到菜单编辑界面
            // $lxh.redirect($lxh.url().makeAction('List'), 500)
        })

        // 发起修改或新增操作
        model.save()

    }
})