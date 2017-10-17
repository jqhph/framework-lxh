/**
 * 列表项多选
 * Created by Jqh on 2017/7/30.
 */
define([], function () {
    var view = {
        init: function () {
            this.$fields = $('.fields-radio')
            this.$items = this.$fields.find('a.btn')

            this.$items.click(this.events.select.bind(this))

            this.$input = $('#align-search')

            var values = this.$input.val()

            if (values) {
                // 默认选中已搜索选项
                var $box = this.$input.parent()
                values = values.split(',')
                for (var i in values) {
                    $box.find('[data-value="'+ values[i] +'"]').removeClass('btn-trans')
                }
            }
        },

        events: {
            select: function (e) {
                var $this = $(e.currentTarget),
                    $all = $this.parent().find('a.btn'),
                    $input = $this.parent().find('input[type="hidden"]'),
                    inputVal = $input.val(),
                    i, data = []

                $this.toggleClass('btn-trans')

                if ($this.hasClass('btn-trans') && inputVal) {
                    inputVal = inputVal.split(',')
                    for (i in inputVal) {
                        if (inputVal[i] == $this.attr('data-value')) continue
                        data.push(inputVal[i])
                    }
                    inputVal = data.join(',')
                } else {
                    if (inputVal) {
                        inputVal += ',' + $this.attr('data-value')
                    } else {
                        inputVal = $this.attr('data-value')
                    }
                }

                // 表单赋值
                $input.val(inputVal)
            }
        },
    }

    $(function () {
        view.init()
    })
    return view
})