/**
 * Created by Jqh on 2017/7/19.
 */
define(['blade'], function () {
    var modules = {
        store: {
            formSelector: '.System-form',

            inputValues: {},
            moduleInfoValidator: null,
            fieldsExtraBlade: null,
        },

        init: function () {
            this.$nextButton = $('a[data-action="next-tab"]')
            this.$prevButton = $('a[data-action="prev-tab"]')
            this.$tab = $('a[data-action="tab"]')

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

        // 保存模块信息
        saveModuleInfo: function (e) {
            current ++;

            compute_progressbar_percent(current)

            // 显示下个tab页
            $('a[data-action="tab"]').eq(current).tab('show')

            this.store.inputValues = $lxh.form().get(this.store.formSelector)

            // console.log(123, this.store.inputValues)
        },

        // 字段编辑信息
        editTable: function () {
            var $editTableBody = $('.edit-fields table tbody'),
                rowTpls = $('#add-fields-edit-rows').text(),
                blade = new Blade(rowTpls),
            // 序号
                no = 1

            // 添加下一行字段信息
            $('button[data-action="addToTable"]').click(function () {
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
        addFieldsExtraEditRows: function (fieldName) {
            if (! this.store.fieldsExtraBlade) {
                var fieldsExtraRowTpl = $('#fields-extra-edit-rows').text(),
                    $fieldsExtraTableBody = $('.fields-extra table tbody')

                    this.store.fieldsExtraBlade = new Blade(fieldsExtraRowTpl)
            }

            this.store.fieldsExtraBlade.assign('fieldName', fieldName)

            $fieldsExtraTableBody.append(this.store.fieldsExtraBlade.fetch())
        }
    }


    // 计算进度条百分比
    function compute_progressbar_percent(current) {
        var $percent = ((parseInt(current) + 1) / total) * 100;
        // console.log(total, current, $percent)
        $('#progressbarwizard').find('.bar').css  ({width:$percent+'%'})
    }

    // 当前tab显示位置
    var current = 0,
    // tab数量
        total = 3

    window.lxh_action = function () {
        modules.init()

        compute_progressbar_percent(current)


        modules.$prevButton.click(function (e) {
            if (current < 1) return

            current --;

            compute_progressbar_percent(current)

            e.preventDefault()

            modules.$tab.eq(current).tab('show')

        })

        modules.$nextButton.click(function (e) {
            if (current > total - 2) return

            e.preventDefault()

            // 手动验证表单
            modules.store.moduleInfoValidator._validateForm('submit')

            if (current == 2) {

            }
        })
    }
})
