<script>
//function add_js()
//{
//    return ['test111']
//}
//function add_lang_scopes() {
//    return ['User']
//}
function lxh_action(plugIns) {
    var v = $lxh.formValidator([
        {
            name: 'username',
            rules: 'length_between[4-20]',
        },
        {
            name: 'password',
            rules: 'length_between[4-30]'
        },

    ], submit)

    var model = $lxh.createModel('User')
    var notify = $lxh.ui.notify()

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

            $lxh.redirect('/', 500)
        })
        // 发起登录请求
        model.touchAction('Login', 'POST')

    }
}
</script>