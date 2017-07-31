<script>
//function add_js()
//{
//    return ['test111']
//}
//function add_lang_scopes() {
//    return ['User']
//}
add_action(function (plugIns) {
    var v = $lxh.validator([
        {
            name: 'username',
            rules: 'required|length_between[4-20]',
        },
        {
            name: 'password',
            rules: 'required|length_between[4-30]'
        },

    ], submit, '.User-form')

    var model = $lxh.createModel('User')
    var notify = $lxh.ui().notify()

    function submit(e) {
        if (! model.requestEnded()) {
            return notify.warning(trans('Logging in, please wait a moment'))
        }

        notify.remove()
        notify.info(trans('loading'))

        // 设置成功回调函数
        model.on('success', function (data) {
            // success
            notify.remove()
            notify.success(trans('login success'))

            // 500豪秒后跳转到首页
            $lxh.redirect('/', 500)
        })
        // 发起登录请求
        model.touchAction('Login', 'POST')

    }
})
</script>