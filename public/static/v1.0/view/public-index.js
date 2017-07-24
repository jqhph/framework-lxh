/**
 * index界面公共js
 *
 * Created by Jqh on 2017/7/19.
 */

define(['css/sweet-alert.css', 'lib/js/sweet-alert.min'], function () {
    var model = null
    var public = {
        delete: function (e) {
            var $this = $(e.currentTarget),
                id = $this.attr('data-id'),
                modelName = $this.attr('data-model') || $lxh.controllerName()
            if (! id) {
                throw new Error('Missing id.')
            }
            if (! model) {
                model = $lxh.createModel(modelName)
            }

            model.set('id', id)

            model.on('success', function () {
                swal({
                    title: trans("Deleted!"),
                    text: trans("The row has been deleted.", 'tip'),
                    type: "success"
                }, function () {
                    window.location.reload()
                });

            })

            // 确认窗
            swal({
                title: trans("Are you sure?", 'tip'),
                text: trans("You will not be able to recover this row!", 'tip'),
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: 'btn-danger',
                confirmButtonText: trans("Yes, delete it!", 'tip'),
                cancelButtonText: trans('Cancel'),
                closeOnConfirm: false
            }, function () {
                // 发起删除请求
                model.delete()
            });

        },
        
    }

    // 绑定删除事件
    $('a[data-action="delete-row"]').click(public.delete)
})
