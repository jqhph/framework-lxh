add_action(function () {
    var v = $lxh.validator(window.formRules || [], submit)

    var model = $lxh.createModel()
    var notify = $lxh.ui().notify()

    function submit(e) {
        notify.remove()
        var $loading = window.loading()

        // 设置成功回调函数
        model.on('success', function (data) {
            // success
            $loading.close()
            notify.success(trans('success'))

            // 500豪秒后跳转到菜单编辑界面
            var id, name = ''
            if (id = model.get('id')) {
                name = 'edit-' + $lxh.controllerName() + '-' + id
            } else {
                name = 'create-' + $lxh.controllerName()
                close_tab(name)
            }

        })
        model.on('failed', function () {
            if (typeof swal != 'undefined') swal.close() // 关闭提示窗
            notify.remove()
            notify.error(trans(data.msg, 'tip'))
            $loading.close()
        })

        // 发起修改或新增操作
        model.save()
    }
})