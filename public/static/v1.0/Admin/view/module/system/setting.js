/**
 * Created by Jqh on 2017/7/28.
 */
define(['css/sweet-alert.css', 'lib/js/sweet-alert.min'], function () {
    var view = {
        init: function () {
            var model = $lxh.createModel(), notify = $lxh.ui().notify()
            model.on('success', function () {
                swal.close()
                notify.remove()
                notify.success(trans('Success'))
                setTimeout(function () {
                    // 清除缓存成功，重新加载界面
                    location.reload()
                }, 500)
            })

            // 清除前端缓存
            $('a[data-action="clear-client-cache"]').click(function () {
                this.confirm(function () {
                    notify.info(trans('loading'))
                    model.touchAction('clear-client-cache')
                })
            }.bind(this))

            // 清除所有缓存
            $('a[data-action="clear-js-css-cache"]').click(function () {
                this.confirm(function () {
                    notify.info(trans('loading'))
                    model.touchAction('clear-all-client-cache')
                })
            }.bind(this))
        },
        confirm: function (call) {
            // 确认窗
            swal({
                title: trans("Are you sure?", 'tip'),
                text: trans("You will not be able to recover this row!", 'tip'),
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: 'btn-danger',
                confirmButtonText: trans("Yes", 'tip'),
                cancelButtonText: trans('Cancel'),
                closeOnConfirm: false
            }, call);
        },
        events: {

        }
    }

    add_action(function () {
        view.init()
    })
    return view
})