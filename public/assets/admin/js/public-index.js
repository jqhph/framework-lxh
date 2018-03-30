/**
 * index界面公共js
 *
 * Created by Jqh on 2017/7/19.
 */

(function () {
    var model = null, listids, sut = {
        anim: 4,
        icon: 1,
        offset:'t',
        skin: 'layer-ext-moon'
    },
        ntf;
    var public = {
        delete: function (e, method, title) {
            method = method || 'delete';
            title = title || "Permanently delete";

            var $this = $(e.currentTarget),
                id = $this.attr('data-id'),
                modelName = $this.attr('data-model') || $lxh.controllerName();
            if (! id) {
                throw new Error('Missing id.')
            }
            if (! model) {
                model = $lxh.createModel(modelName);
            }

            model.setId(id);

            model.on('success', function () {
                layer.msg(trans("Deleted!"), sut);
                reload_grid();
            });

            var rowText = $this.parent().parent().text();
            if (rowText) rowText = rowText.replace(/[\n]|[\s]]/gi, ' ') + "\n";
            // 确认窗
            layer.confirm(rowText, {
                title: trans(title),
                icon: 0,
                skin: 'layer-ext-moon',
                btn: [trans("Done"), trans('Cancel')] //按钮
            }, function(){
                // 发起删除请求
                model[method]()
            });

        },
        // 批量删除
        batchDelete: function (e, method, title) {
            method = method || 'batchDelete';
            title = title || 'Permanently delete';

            if (! listids) {
                return ntf.error(trans('Unchecked!'));
            }
            var modelName = $(e.currentTarget).attr('data-model'),
                model = $lxh.createModel(modelName);

            model.set('ids', listids);

            model.on('success', function () {
                layer.msg(trans("Deleted!"), sut);
                reload_grid();
            });

            // 确认窗
            layer.confirm(listids, {
                title: trans(title),
                icon: 0,
                skin: 'layer-ext-moon',
                btn: [trans("Done"), trans('Cancel')] //按钮
            }, function(){
                // 发起删除请求
                model[method]()
            });
        },
        // 移至回收站
        moveToTrash: function (e) {
            this.delete(e, 'moveToTrash', 'Move to trash');
        },
        // 还原
        restore: function (e) {
            var $this = $(e.currentTarget),
                id = $this.attr('data-id'),
                modelName = $this.attr('data-model') || $lxh.controllerName();
            if (! id) {
                throw new Error('Missing id.')
            }
            if (! model) {
                model = $lxh.createModel(modelName);
            }

            model.set('ids', id);

            model.on('success', function () {
                layer.msg(trans("Update succeeded!"), sut);
                reload_grid();
            });
            model.restore();
        },
        // 永久删除
        deletePermanently: function (e) {
            this.delete(e);
        },
        // 批量移至回收站
        batchMoveToTrash: function (e) {
            this.batchDelete(e, 'batchMoveToTrash', 'Move to trash')
        },
        // 批量还原
        batchRestore: function (e) {
            if (! listids) {
                return ntf.error(trans('Unchecked!'));
            }
            var modelName = $(e.currentTarget).attr('data-model'),
                model = $lxh.createModel(modelName);

            model.set('ids', listids);

            model.on('success', function () {
                layer.msg(trans("Update succeeded!"), sut);
                reload_grid();
            });
            model.restore();
        },
        // 批量永久删除
        batchDeletePermanently: function (e) {
            this.batchDelete(e)
        },
        // 快速编辑
        quickEdit: function (e) {
            var _t = $(e.currentTarget), tr = _t.parents('.row-list');
            tr.hide();
            $('.edit-' + tr.data('id')).show();
            LXHSTORE.IFRAME.height();
        },
        // 取消快速编辑
        quickEditCancel: function (e) {
            var _t = $(e.currentTarget), id = _t.data('id');
            $('.edit-' + id).hide();
            $('tr[data-id="'+id+'"]').show();
            return false; 
        },
    };

    // 绑定删除事件
    $('a[data-action="delete-row"]').click(public.delete.bind(public));
    $('a[data-action="restore"]').click(public.restore.bind(public));
    $('a[data-action="delete-permanently"]').click(public.deletePermanently.bind(public));
    $('a[data-action="trash"]').click(public.moveToTrash.bind(public));
    $('.batch-delete').click(public.batchDelete.bind(public));
    $('.batch-to-trash').click(public.batchMoveToTrash.bind(public));
    $('.batch-restore').click(public.batchRestore.bind(public));
    $('.batch-delete-permanently').click(public.batchDeletePermanently.bind(public));
    $('.quick-edit-btn').click(public.quickEdit.bind(public));
    $('.quick-edit .cancel').click(public.quickEditCancel.bind(public));
    __then__(function () {
        ntf = $lxh.ui().notify();
        // 行选择器点击事件
        var allInput = $('input[data-action="select-all"]');

        $(document).on('pjax:complete', function () {
            // 绑定删除事件
            $('a[data-action="delete-row"]').click(public.delete.bind(public));
            $('a[data-action="restore"]').click(public.restore.bind(public));
            $('a[data-action="delete-permanently"]').click(public.deletePermanently.bind(public));
            $('a[data-action="trash"]').click(public.moveToTrash.bind(public));
            $('.quick-edit-btn').click(public.quickEdit.bind(public));
            $('.quick-edit .cancel').click(public.quickEditCancel.bind(public));

            allInput = $('input[data-action="select-all"]');
            // 反选点击事件
            allInput.click(selectall);
            // 单行选中事件
            $('input[name="tb-row[]"]').click(selecone);
        });

        // 反选点击事件
        allInput.click(selectall);
        // 单行选中事件
        $('input[name="tb-row[]"]').click(selecone);

        function selectall() {
            var _this = $(this), tb = _this.parent().parent().parent().parent(), inputs = tb.find('input[name="tb-row[]"]');
            if (_this.prop('checked')) {
                // 选中所有行，并把所有行的id存储到本按钮value中
                inputs.prop('checked', true);
                var ids = [], i, id;
                inputs.each(function(i, input) {
                    if (! (id = notinvalid(input))) return;
                    ids.push(id);
                    active($(input)); // 添加选中效果
                });
                set_all_input(ids.join(','));

            } else {
                inputs.prop('checked', false);
                set_all_input(''); // 清除值
                for (i in inputs) {
                    if (! notinvalid(inputs[i])) continue;
                    active($(inputs[i]), false) // 移除选中效果
                }
            }
        }
        // 验证input对象是否无效
        function notinvalid(input) {
            if (typeof input != 'object' || typeof input == 'function' || typeof $(input).val == 'undefined' || ((input = $(input).val()) == 'on')) return false;
            return input
        }
        function set_all_input(val) {
            listids = val;
            allInput.val(val);
            $(document).trigger('grid.selected', val);
        }
        function selecone() {
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
        }
        // 给当前行添加选中效果
        function active(input, close) {
            if (input.data('action') == 'select-all') return;
            var tr = input.parent().parent();
            tr.removeClass('active');
            if (close !== false) tr.addClass('active');
        }
    })
})();
