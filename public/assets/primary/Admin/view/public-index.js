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
        batchDelete: function () {

            if (! listids) {

            }
            console.log(123, listids)
        }

    };

    // 绑定删除事件
    $('a[data-action="delete-row"]').click(public.delete);
    $('#batch-delete').click(public.batchDelete)
    $(document).on('pjax:complete', function () {
        // 绑定删除事件
        $('a[data-action="delete-row"]').click(public.delete);
    });
    $(document).on('grid.selected', function (e, data) {listids = data});
    add_action(function () {
        // 行选择器点击事件
        (function(){var b=$('input[data-action="select-all"]');b.click(function(){var j=$(this),e=j.parent().parent().parent().parent(),d=e.find('input[name="tb-row[]"]');if(j.prop("checked")){d.prop("checked",true);var g=[],f,h;for(f in d){if(typeof d[f]!="object"||typeof d[f]=="function"||typeof $(d[f]).val=="undefined"){continue}h=$(d[f]).val();if(!h||h=="on"){continue}g.push(h);c($(d[f]))}a(g.join(","))}else{d.prop("checked",false);a('');for(f in d){if(typeof d[f]!="object"||typeof d[f]=="function"||typeof $(d[f]).val=="undefined"){continue}c($(d[f]),false)}}});function a(d){b.val(d);$(document).trigger("grid.selected",d)}$('input[name="tb-row[]"]').click(function(){var e=b.val();e=e?e.split(","):[];if($(this).prop("checked")){e.push($(this).val());c($(this))}else{for(var d in e){if(e[d]==$(this).val()){e.splice(d,1);break}}c($(this),false)}a(e.join(","))});function c(d,f){if(d.data("action")=="select-all"){return}var e=d.parent().parent();e.removeClass("active");if(f!==false){e.addClass("active")}}})();
    })
});
