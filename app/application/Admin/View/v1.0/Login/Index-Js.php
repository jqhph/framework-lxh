<script>
//function add_public_js()
//{
//    return ['test111']
//}
//function add_lang_scopes() {
//    return ['User']
//}
function lxh_action(plugIns) {
    var $parsley = $('form').parsley({});

//    console.log(111, $lxh.cache.get('$$token'), $lxh.cache.storage)

    var model = $lxh.createModel('User')
    var notify = $lxh.ui.notify()

    $('.submit').click(function (e) {
        if (!$parsley.isValid()) {
            return
        }
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

            console.log('success: ', data)
        })
        // 发起登录请求
        model.touchAction('Login', 'POST')

    })
}
</script>