define([''], function () {
    window.lxh_action = function () {
        var v = $lxh.validator([
            {name: 'parent_id', rules: 'required',},
            {name: 'icon', rules: 'length_between[4-30]'},
            {name: 'name', rules: 'length_between[4-30]'},
            {name: 'controller', rules: 'length_between[1-15]'},
            {name: 'action', rules: 'length_between[1-15]'},
            {name: 'priority', rules: 'integer'},
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
                $lxh.redirect($lxh.url().makeAction('Index'), 500)
            })

           // 发起修改操作
           model.edit()

        }
    }
})