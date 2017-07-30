/**
 * 单选搜索
 * Created by Jqh on 2017/7/30.
 */
define([], function () {
    var view = {
        init: function () {
            this.$fields = $('.fields-radio')
            this.$items = this.$fields.find('a.btn')

            this.$items.click(this.events.select.bind(this))
        },

        events: {
            select: function (e) {
                var $this = $(e.currentTarget),
                    $all = $this.parent().find('a.btn'),
                    $input = $this.parent().find('input[type="hidden"]')

                // 给所有按钮重置透明效果
                $all.removeClass('btn-trans')
                $all.addClass('btn-trans')

                // 移除选中按钮的透明效果
                $this.removeClass('btn-trans')

                // 表单赋值
                $input.val($this.attr('data-value'))
            }
        },
    }

    $(function () {
        view.init()
    })
    return view
})