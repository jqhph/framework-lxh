__then__(function () {
    var v = $lxh.validator(window.validatorRules || [], submit)

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
            $lxh.redirect($lxh.url().makeAction('list'), 500)
        })

        // 发起修改或新增操作
        model.save()

    }
})