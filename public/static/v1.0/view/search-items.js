/**
 * Created by Jqh on 2017/7/31.
 */
define([], function () {
    var view = {
        init: function () {
            this.el = '.search-form'
            this.$content = $('.search-card-box-content')
            this.$searchForm = $(this.el)
            this.$inputs = this.$searchForm.find('input')
            this.selects = this.$searchForm.find('select')

            // 初始表单数据
            this.searchFormData = this.getSearchFormData()
            console.log('searchFormData', this.searchFormData)

            // 显示隐藏搜索项
            $('a[data-action="toggle-search-content"]').click(this.events.toggleItems.bind(this))
            // 重置按钮
            $('a[data-action="page-search-reset"]').click(this.events.reset.bind(this))
            // 搜索按钮
            $('a[data-action="page-search"]').click(this.events.search.bind(this))
        },

        // 获取搜索表单数据
        getSearchFormData: function () {
            var data = {
                'input': {},
                'select': {},
            }

            this.$inputs.each(function (k, v) {
                var $this = $(v), name = $this.attr('name')
                if (name && name.indexOf('[') != -1) {
                    // name = name.replace(/[[|]]/, '')
                    if (! data.input[name]) {
                        data.input[name] = []
                    }
                    data.input[name].push($this.val())
                } else if (name) {
                    data.input[name] = $this.val()
                }
            })

            this.selects.each(function (k, v) {
                var $this = $(v), name = $this.attr('name')
                if (name) {
                    data.select[name] = $this.val()
                }
            })
            return data
        },
        events: {
            toggleItems: function (e) {
                var $this = $(e.currentTarget)
                $this.toggleClass('btn-purple')
                $this.toggleClass('btn-danger')

                if ($this.hasClass('btn-purple')) {
                    $this.text(trans('Hidden'))
                } else {
                    $this.text(trans('Show'))
                }
                // 显示、隐藏
                this.$content.animate({height:'toggle'})
            },
            // 重置搜索框
            reset: function (e) {
                $.each(this.searchFormData.input, function (k, v) {
                    if (typeof v == 'object') {
                        this.$searchForm.find('input[name="' + k + '"]').eq(0).val(v[0])
                        this.$searchForm.find('input[name="' + k + '"]').eq(1).val(v[1])
                    } else {
                        var $input = this.$searchForm.find('input[name="' + k + '"]')
                        if ($input.attr('type') == 'hidden') return
                        $input.val(v)
                    }
                }.bind(this))
                $.each(this.searchFormData.select, function (k, v) {
                    this.$searchForm.find('select[name="' + k + '"]').val(v)
                }.bind(this))
            },
            // 搜索
            search: function (e) {
                var url = location.origin + location.pathname
                var data = $lxh.form().get(this.el)
                console.log('data', data)
                console.log(build_http_params(data))
            },
        }
    }

    $(function () {
        view.init()

        // 时间日期插件
        var $dateInputs = $('.date-search-box input')
        if (typeof $dateInputs.datetimepicker == 'function') {
            $dateInputs.datetimepicker({format: 'yyyy-mm-dd hh:ii:ss'})
        }
    })
})