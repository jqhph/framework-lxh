<script>

function LxhAction(plugIns) {
    console.log(345435, plugIns)
    var $parsley = $('form').parsley({});

//    Lxh.createModel('Test').request('/test/Global.json', 'GET')
    var language = $lxh.language

    $('.submit').click(function (e) {
        if (!$parsley.isValid()) {
            return
        }
        var notify = $lxh.ui.notify()
        notify.remove()
        notify.info(language.trans('loading'))

        var model = $lxh.createModel('User')
        // 设置成功回调函数
        model.on('success', function (data) {
            // success
            notify.remove()
            notify.success(language.trans('login success'))

            console.log('success: ', data)
        })
        // 发起登录请求
        model.touchAction('Login', 'POST')

    })
}
</script>