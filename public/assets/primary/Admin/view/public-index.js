/**
 * index界面公共js
 *
 * Created by Jqh on 2017/7/19.
 */

define(['css/sweet-alert.min.css', 'lib/js/sweet-alert.min'], function () {
    var model = null, listids;
    var public = {
        delete: function (e) {
            var $this = $(e.currentTarget),
                id = $this.attr('data-id'),
                modelName = $this.attr('data-model') || $lxh.controllerName();
            if (! id) {
                throw new Error('Missing id.')
            }
            if (! model) {
                model = $lxh.createModel(modelName);
            }

            model.set('id', id);

            model.on('success', function () {
                swal({
                    title: trans("Deleted!", 'tip'),
                    text: trans("The row has been deleted.", 'tip'),
                    type: "success"
                }, function () {
                    window.location.reload();
                });

            });

            var rowText = $this.parent().parent().text();
            if (rowText) rowText = rowText.replace(/[\n]|[\s]]/gi, ' ') + "\n";
            // 确认窗
            swal({
                title: trans("Are you sure to delete the row?", 'tip'),
                text: rowText + trans("You will not be able to recover this row!", 'tip'),
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
        // 批量删除
        batchDelete: function (e) {
            if (! listids) {
                return $lxh.ui().notify().error(trans('Unchecked!', 'tip'));
            }
            var modelName = $(e.currentTarget).attr('data-model'),
                model = $lxh.createModel(modelName);

            model.set('ids', listids);

            model.on('success', function () {
                swal({
                    title: trans("Deleted!", 'tip'),
                    type: "success"
                }, function () {
                    window.location.reload();
                });
            });

            // 确认窗
            swal({
                title: trans("Are you sure to delete these rows?", 'tip'),
                text: listids + "\n" + trans("You will not be able to recover these rows!", 'tip'),
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: 'btn-danger',
                confirmButtonText: trans("Yes, delete it!", 'tip'),
                cancelButtonText: trans('Cancel'),
                closeOnConfirm: false
            }, function () {
                model.batchDelete()
            });
        }

    };

    // 绑定删除事件
    $('a[data-action="delete-row"]').click(public.delete);
    $('#batch-delete').click(public.batchDelete)
    $(document).on('pjax:complete', function () {
        // 绑定删除事件
        $('a[data-action="delete-row"]').click(public.delete);
    });
    add_action(function () {
        // 行选择器点击事件
        (function () {
            var allInput = $('input[data-action="select-all"]')
            // 选中所有行checkbox点击事件
            allInput.click(function () {
                var _this = $(this), tb = _this.parent().parent().parent().parent(), inputs = tb.find('input[name="tb-row[]"]');
                if (_this.prop('checked')) {
                    // 选中所有行，并把所有行的id存储到本按钮value中
                    inputs.prop('checked', true);
                    var ids = [], i, id;
                    for (i in inputs) {
                        if (typeof inputs[i] != 'object' || typeof inputs[i] == 'function' || typeof $(inputs[i]).val == 'undefined') continue;
                        id = $(inputs[i]).val();
                        if (!id) continue;
                        ids.push(id);
                        active($(inputs[i])); // 添加选中效果
                    }
                    set_all_input(ids.join(','));

                } else {
                    inputs.prop('checked', false);
                    set_all_input(''); // 清除值
                    for (i in inputs) {
                        if (typeof inputs[i] != 'object' || typeof inputs[i] == 'function' || typeof $(inputs[i]).val == 'undefined') continue;
                        active($(inputs[i]), false) // 移除选中效果
                    }
                }
            });
            function set_all_input(val) {
                listids = val;
                allInput.val(val);
                $(document).trigger('grid.selected', val);
            }
            // 单行选中事件
            $('input[name="tb-row[]"]').click(function () {
                var ids = allInput.val(), $this = $(this), id = $this.val();
                ids = (ids !== 'on' && ids) ? ids.split(',') : [];
                if ($this.prop('checked')) {
                    if (id) ids.push(id);
                    active($this);
                } else {
                    for(var i in ids) {
                        if(ids[i] == id) {
                            ids.splice(i, 1);
                            break;
                        }
                    }
                    active($this, false);
                }
                set_all_input(ids.join(','))
            })
            // 给当前行添加选中效果
            function active(input, close) {
                if (input.data('action') == 'select-all') return;
                var tr = input.parent().parent();
                tr.removeClass('active');
                if (close !== false) tr.addClass('active');
            }
        })()    })
});
