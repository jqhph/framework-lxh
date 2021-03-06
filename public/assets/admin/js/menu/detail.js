__then__(function () {
    var v = $lxh.validator([
        {name: 'parent_id', rules: 'required',},
        {name: 'icon', rules: 'length_between[4-30]'},
        {name: 'name', rules: 'required|length_between[4-30]'},
        {name: 'controller', rules: 'length_between[1-15]'},
        {name: 'action', rules: 'length_between[1-15]'},
        {name: 'priority', rules: 'required|integer'},
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
            // $lxh.redirect($lxh.url().makeAction('list'), 500)
            var id, name = ''
            if (id = model.get('id')) {
                name = 'edit-' + $lxh.controllerName() + '-' + id
            } else {
                name = 'create-' + $lxh.controllerName()
                close_tab(name)
            }

        })

        // 发起修改或新增操作
        model.save()

    }
})