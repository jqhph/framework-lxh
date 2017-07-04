<script>
    function lxh_action() {
        var language = $lxh.language

        var v = $lxh.formValidator([
            {
                name: 'username',
                rules: 'required|min_length[6]'
            },
            {
                name: 'password',
                rules: 'required'
            },
            {
                name: 'repassword',
                rules: 'required|matches[password]'
            },
            {
                name: 'terms',
                rules: 'required'
            },

        ], submit)

        var notify = $lxh.ui.notify()
        var model = $lxh.createModel('User')

        function submit(e) {
            if (! model.requestEnded()) {
                return
            }

            notify.remove()
            notify.info(language.trans('loading'))

            // 设置成功回调函数
            model.on('success', function (data) {
                // success
                notify.remove()
                notify.success(language.trans('Successful registration.'))

                console.log('success: ', data)
            })
            // 发起登录请求
            model.touchAction('Register', 'POST')

        }
    }
</script>