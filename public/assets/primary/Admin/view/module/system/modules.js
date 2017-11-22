/**
 * Created by Jqh on 2017/7/19.
 */
define(['blade'], function () {
    // 添加自定义标签
    BladeConfig.addTag('view', function ($view, name, fieldName, i) {
        var blade = new Blade($('#' + name).text())

        // 第一次，用+号按钮
        var plusBtn = '<i data-name="' + fieldName + '" data-action="add-key-value-row" class="fa fa-plus" style="color:#0eac5c;cursor:pointer"></i>'

        return blade.fetch({field: fieldName, btn: plusBtn})
    })

    // 当前tab显示位置
    var current = 0,

    // tab数量
    total = 3,

    modules = {
        store: {
            formSelector: '.System-form',

            inputValues: {},
            addFieldsValidator: null,
            moduleInfoValidator: null,
            fieldsExtraValidator: null,
            fieldsExtraBlade: null,
        },

        init: function () {
            this.$nextButton = $('a[data-action="next-tab"]')
            this.$prevButton = $('a[data-action="prev-tab"]')
            this.$saveButton = $('a[data-action="save"]')
            this.$tab = $('a[data-action="tab"]')
            this.$fieldsExtraTableBody = $('.fields-extra table tbody')
            this.$addButton = $('button[data-action="addToTable"]')

            // 创建模型
            this.model = $lxh.createModel()

            // 绑定成功回调事件
            this.model.on('success', this.success.bind(this))

            this.editTable()

            // 给添加模块信息tab表单添加字段验证信息
            this.store.moduleInfoValidator = $lxh.validator([
                {name: 'controller_name', rules: 'required|length_between[1-10]',},
                {name: 'en_name', rules: 'required|length_between[1-20]',},
                {name: 'zh_name', rules: 'required|length_between[1-20]',},
                {name: 'icon', rules: 'length_between[4-30]'},
                {name: 'author', rules: 'length_between[1-20]'},
                {name: 'module', rules: 'required'},
                {name: 'inheritance', rules: 'required'},
                {name: 'limit', rules: 'required|integer'},
            ], this.saveModuleInfo.bind(this), this.store.formSelector)
        },

        events: {
            // 保存按钮点击事件
            save: function (e) {
                this.store.inputValues = $lxh.form().get(this.store.formSelector)

                // 设置接口请求数据
                this.model.data(this.store.inputValues)

                // 发起创建模块请求
                this.model.touchAction('create-module', 'POST')
            },

            prevTab: function (e) {
                if (current < 1) return

                current --;

                compute_progressbar_percent(current)

                e.preventDefault()

                this.$tab.eq(current).tab('show')

                this.$nextButton.show()
                this.$saveButton.hide()
            },

            nextTab: function (e) {
                if (current > total - 2) return

                e.preventDefault()

                // 手动验证表单
                this.store.moduleInfoValidator._validateForm('submit')

                // 最后一个tab页，显示保存按钮
                if (current == total - 1) {
                    this.$saveButton.show()
                    this.$nextButton.hide()

                    // 判断是否添加了字段信息
                    if (typeof this.store.inputValues.field_name == 'undefined') {
                        return $lxh.ui().notify().error(trans('Invalid argument', 'tip'))
                    }

                    if (this.store.inputValues.field_name.length < 1) {
                        return $lxh.ui().notify().error(trans('Please add a field at least', 'tip'))
                    }

                    this.makeFieldsExtraEditRows(modules.store.inputValues.field_name)

                    this.displayFieldsExtraForm(modules.store.inputValues)
                } else {
                    this.$nextButton.show()
                }
            }
        },

        // 生成模块成功回调事件
        success: function (data) {
console.log('Success', data)
        },

        // 保存模块信息
        saveModuleInfo: function (e) {
            current ++;

            compute_progressbar_percent(current)

            // 显示下个tab页
            this.$tab.eq(current).tab('show')

            // 保存表单数据
            this.store.inputValues = $lxh.form().get(this.store.formSelector)

            // console.log(123, this.store.inputValues)
        },

        // 字段编辑信息
        editTable: function () {
            var $editTableBody = $('.edit-fields table tbody'),
                rowTpls = $('#add-fields-edit-rows').text(),
                blade = new Blade(rowTpls),
                // 序号
                no = 1,
                self = this

            // 添加下一行字段信息
            this.$addButton.click(function () {
                // 计算tr行数
                no = $editTableBody.find('tr').length

                no ++

                blade.assign('no', no)

                $editTableBody.append(blade.fetch())

                var $row = $('i[data-action="remove-edit-row"]')

                $row.unbind('click')
                $row.click(function (e) {
                    $(e.currentTarget).parent().parent().remove()
                })

            })
        },

        // 字段拓展信息
        addFieldsExtraEditRow: function (fieldName) {
            if (! this.store.fieldsExtraBlade) {
                var fieldsExtraRowTpl = $('#fields-extra-edit-rows').text()

                this.store.fieldsExtraBlade = new Blade(fieldsExtraRowTpl)
            }

            this.store.fieldsExtraBlade.assign('fieldName', fieldName)

            this.$fieldsExtraTableBody.append(this.store.fieldsExtraBlade.fetch())
        },

        // 生成字段额外配置表单
        makeFieldsExtraEditRows: function (fields) {
            this.$fieldsExtraTableBody.html('')

            for (var i in fields) {
                this.addFieldsExtraEditRow(fields[i])
            }

        },

        // 显示额外字段配置表单
        alreadyDisplayOptionFields: {},
        displayFieldsExtraForm: function (values) {
            // 先隐藏每个项
            $('.field-options .field-options-item').hide()

            for (var i in values.field_type) {
                var fieldName = values.field_name[i]
                // 枚举类型，需要添加键值对表单
                if (values.field_type[i].indexOf('enum') != -1) {
                    // 如果有枚举类型字段才显示option表单
                    $('.field-options .field-options-' + fieldName).show()

                    if (typeof this.alreadyDisplayOptionFields[fieldName] == 'undefined') {
                        // 如果未生成option表单，则先生成
                        this.displayFieldOptions(fieldName, values.field_type[i], i)
                        this.alreadyDisplayOptionFields[fieldName] = 1
                    }
                }
            }
        },

        // 显示枚举类型字段options键值对表单
        displayFieldOptions: function (name, type, i) {
            var blade = new Blade($('#fields-extra-options').text()),
                $options = $('.field-options')

            $options.show()
            $options.append(blade.fetch({field: name, i: i}))

            var $addBtn = $('i[data-action="add-key-value-row"]'),
                inputTpl = $('#fieldsExtraOptionsInput').text()

            $addBtn.unbind('click')

            // 添加options键值对表单
            $addBtn.click(function (e) {
                var $this = $(e.currentTarget),
                    fieldName = $this.attr('data-name')

                var closeBtn = '<i data-name="' + fieldName + '" data-action="remove-key-value-row" class="fa fa-times" style="color:#ff5b5b;cursor:pointer"></i>',
                    blade = new Blade(inputTpl, {field: fieldName, btn: closeBtn})
                $options.find('.field-options-' + fieldName).append(blade.fetch())

                var $closeBtn = $('i[data-action="remove-key-value-row"]')
                $closeBtn.unbind('click')

                // 移除表单
                $closeBtn.click(function (e) {
                    $(e.currentTarget).parent().parent().remove()
                })
            })
        }
    }


    // 计算进度条百分比
    function compute_progressbar_percent(current) {
        var $percent = ((parseInt(current) + 1) / total) * 100;
        // console.log(total, current, $percent)
        $('#progressbarwizard').find('.bar').css  ({width:$percent+'%'})
    }

    add_action(function () {
        modules.init()

        compute_progressbar_percent(current)

        // 跳到上个tab页
        modules.$prevButton.click(modules.events.prevTab.bind(modules))

        // 跳到下个tab页
        modules.$nextButton.click(modules.events.nextTab.bind(modules))

        // 生成新的模块
        modules.$saveButton.click(modules.events.save.bind(modules))
    })
})
