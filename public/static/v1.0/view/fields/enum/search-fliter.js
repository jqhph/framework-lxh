/**
 * 带搜索功能单选下拉框
 * Created by Jqh on 2017/7/31.
 */
define([], function () {
    var view = {
        init: function () {
            this.$fields = $('.fields-radio-search')
            this.$select = this.$fields.find('select')
            // 所有option text值数组
            this.optionTexts = []
            // text对应value对象
            this.data = {}

            this.normalize()

            this.$fields.find('input').keyup(this.events.search.bind(this))
        },

        normalize: function () {
            $.each(this.$select.find('option'), function (k, v) {
                var $this = $(v),
                    text = $this.text(),
                    value = $this.attr('value')
                this.optionTexts.push(text)
                this.data[text] = value
            }.bind(this))
        },

        events: {
            search: function (e) {
                var $options = this.$select.find('option'), text = $(e.currentTarget).val(), i, selectedText = '', likeTexts = [], value
                $options.show()

                for (i in this.optionTexts) {
                    if (text == this.optionTexts[i]) {
                        selectedText = text
                        break
                    }
                    if (text && this.optionTexts[i].indexOf(text) != -1) {
                        likeTexts.push(this.optionTexts[i])
                    }
                }

                if (selectedText) {
                    // 有完全选中的值，直接选中下拉框
                    value = this.data[selectedText]
                    return this.$select.find('option[value="' + value + '"]').prop('selected', true)
                }

                if (likeTexts.length > 0) {
                    $options.hide()
                    for (i in likeTexts) {
                        this.$select.find('option[value="' + this.data[likeTexts[i]] + '"]').show()
                    }
                }
            }
        },
    }

    $(function () {
        view.init()
    })
    return view
})