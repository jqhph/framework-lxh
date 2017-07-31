/**
 * Created by Jqh on 2017/7/21. , 'css/sweet-twitter.css'
 */
define(['blade', 'css/sweet-alert.css', 'lib/js/sweet-alert'], function () {
    var language = {
        // 初始化方法
        init: function () {
            // 添加自定义标签
            BladeConfig.addTag('view', function ($view, options, tpl) {
                var name = options[0],
                    data = options[1],
                    category = options[2]

                if (typeof data != 'object') return ''

                var blade = new Blade($('#' + name).text())

                return blade.fetch({list: data, cate: category})
            })

            this.$languageTable = $('.language-table')
            this.$languageBody = this.$languageTable.find('tbody')
            this.$packageTitle = $('.package-title')
            this.$saveButton = $('button[data-action="save"]')
            this.$cancelButton = $('button[data-action="cancel"]')
            this.languageBlade = new Blade($('#row-tpl').text())
            this.model = $lxh.createModel()
            this.saveModel = $lxh.createModel()

            this.notify = $lxh.ui().notify()

            // 注册成功回调事件
            this.saveModel.on('success', this.saveSuccess.bind(this))
            this.model.on('success', this.renderLanguageList.bind(this))

            // 初始化语言包树
            $('.basic-language').jstree({
                'core' : {
                    'themes' : {'responsive': true},
                    // 'check_callback': false
                },
                'types' : {
                    'default' : {'icon' : 'zmdi zmdi-folder folder',},
                    'file' : {'icon' : 'zmdi zmdi-file file'}
                },
                'plugins' : ['types', 'wholerow', 'sort', 'ui']
            })

            // 语言包文件点击事件
            $('a.jstree-anchor').on("click.jstree", this.events.leafClick.bind(this))

            $('button[data-action="edit"]').click(this.events.editTable.bind(this))
            $('button[data-action="create-category"]').click(this.events.createCategory.bind(this))
            $('button[data-action="create-value"]').click(this.events.createValue.bind(this))
            $('button[data-action="create-file"]').click(this.events.createFile.bind(this))
            $('button[data-action="copy-file"]').click(this.events.copyFile.bind(this))
            $('button[data-action="create-options"]').click(this.events.createOptions.bind(this))
            this.$saveButton.click(this.events.save.bind(this))
            this.$cancelButton.click(this.events.cancel.bind(this))
        },

        // 切换编辑模式
        toggleTableInput: function (show) {
            var method = show ? 'show' : 'hide'
            var spanMethod = show ? 'hide' : 'show'

            // 隐藏或显示表单
            this.$languageBody.find('input')[method]()
            this.$languageBody.find('span.text')[spanMethod]()

            this.$languageTable.find('.remove-td')[method]()

            // 隐藏或显示保存和取消按钮
            this.$saveButton[method]()
            this.$cancelButton[method]()
        },

        // 保存成功回调模式
        saveSuccess: function (data) {
            this.notify.remove()

            this.saveData.content = JSON.parse(this.saveData.content)
            this.renderLanguageList(this.saveData)

            this.notify.success(trans('success'))

            this.toggleTableInput(false)
        },

        // 格式化表单数据
        normalizeFormData: function (data) {
            var content = {}, i, c
            // 先转化第一层
            for (i in data) {
                if (i == 'origin_category') {
                    for (c in data[i]) {
                        content[data[i][c]] = {}
                    }
                    content = get_name_and_value(content, data)
                }
            }

            for (i in data) {
                if (i == 'category') {
                    for (c in data[i]) {
                        if (data[i][c] == data['origin_category'][c]) continue
                        content[data[i][c]] = content[data['origin_category'][c]]
                        delete content[data['origin_category'][c]]
                    }
                }
            }

            return content

            function get_name_and_value(content, data, useValue, changes) {
                var nameKey, valueKey, tmp, i, j, newContent = {}
                for (i in content) {
                    tmp = {}
                    if (useValue) {
                        nameKey = content[i] + '_value_name'
                        valueKey = content[i] + '_value'
                    } else {
                        nameKey = i + '_value_name'
                        valueKey = i + '_value'
                    }

                    if (data[nameKey] && data[valueKey]) {
                        for (j in data[nameKey]) {
                            tmp[data[nameKey][j]] = data[valueKey][j]
                        }
                        if (useValue) {
                            if (! changes[i]) {
                                throw new Error('This field is required.')
                            }
                            newContent[changes[i]] = tmp

                        } else {
                            newContent[i] = tmp
                        }

                    } else if(data[nameKey]) {
                        newContent[i] = get_name_and_value(data['origin_' + nameKey], data, true, data[nameKey])
                        // console.log(2333, get_name_and_value(data['origin_' + nameKey], data, true))
                    } else {
                        newContent[i] = {}
                    }
                }

                return newContent
            }
        },

        events: {
            save: function (e) {
                // 获取表单数据
                var data = this.saveModel.getFormData()
                var path = this.$packageTitle.text()

                if (! path) return this.notify.error(trans('Missing path.'))

                data = this.normalizeFormData(data)

                this.notify.info(trans('loading'), 0)

                this.saveData = {content: JSON.stringify(data), path: path}

                this.saveModel.data(this.saveData)

                this.saveModel.touchAction('Save', 'POST')
            },
            removeRow: function (e) {
                // 确认窗
                swal({
                    title: trans("Are you sure?", 'tip'),
                    text: trans("You will not be able to recover this row!", 'tip'),
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonText: trans('Cancel'),
                    confirmButtonClass: 'btn-danger',
                    confirmButtonText: trans("Yes, delete it!", 'tip'),
                    closeOnConfirm: false
                }, function () {
                    $(e.currentTarget).parent().parent().remove()
                    swal.close()
                });
            },

            // 退出编辑状态
            cancel: function (e) {
                this.toggleTableInput(false)
            },
            // 切换编辑状态
            editTable: function (e) {
                // 判断是否选择了文件
                if (this.$packageTitle.text().indexOf('/') == -1) {
                    return false
                }
                this.toggleTableInput(true)
            },
            createCategory: function (e) {
                var path = this.$packageTitle.text()

                // 判断是否选择了文件
                if (path.indexOf('/') == -1) {
                    this.notify.error(trans('Please select a file first', 'tip'))
                    return swal.close()
                }

                // 弹窗
                this.modal("Create Category", $('#createCategoryTpl').text(), 'Create', function () {
                    var name = $('input[name="cate_name"]').val()

                    if (! name) {
                        return this.notify.error(trans('The category name is required', 'tip'))
                    }

                    this.notify.info(trans('loading'))

                    var model = $lxh.createModel()
                    model.on('success', function (data) {
                        swal.close()

                        this.renderLanguageList(data)

                        this.notify.success(trans('success'))

                    }.bind(this))

                    model.data({path: path, name: name})

                    model.touchAction('CreateCategory', 'POST')

                })

            },

            // 创建普通语言包键值对
            createValue: function (e) {
                var path = this.$packageTitle.text()

                // 判断是否选择了文件
                if (path.indexOf('/') == -1) {
                    return this.notify.error(trans('Please select a file first', 'tip'))
                }
                // 获取表单数据
                var data = this.saveModel.getFormData(),
                    categories = [],
                    keyValueTpl = $('#addKeyValueTpl').text(),
                    blade = new Blade($('#createValueTpl').text()),
                    model = $lxh.createModel(),
                    notify = this.notify

                for (var i in data.category) {
                    categories.push(data.category[i])
                }

                // 创建弹出窗
                var modal = $lxh.ui().modal({
                    title: 'Create Value',
                    content: blade.fetch({categories: categories}),
                    saveButtonLabel: 'Create',
                    class: '',
                }, function (modal) {
                    var formData = $lxh.form().get('.modal-container')

                    try {
                        formData = normalize(formData)
                    } catch (e) {
                        return notify.error(trans('Invalid arguments', 'tip'))
                    }

                    notify.info(trans('loading'))

                    model.data({path: path, content: formData})

                    // 创建成功回调函数
                    model.on('success', function (data) {
                        // 重新渲染table
                        this.renderLanguageList(data)
                        notify.success(trans('success'))
                        // 关闭弹窗
                        modal.close()
                    }.bind(this))

                    model.touchAction('CreateValue', 'POST')

                    // 格式化
                    function normalize(data) {
                        var newData = {}

                        if (! data.category_name) throw new Error('Invalid arguments')

                        newData[data.category_name] = {}
                        for (i in data.key) {
                            if (! data.key[i] || ! data.value[i]) {
                                throw new Error('Invalid arguments')
                            }
                            newData[data.category_name][data.key[i]] = data.value[i]
                        }
                        return newData
                    }

                }.bind(this))

                $('i[data-action="add-key-value-row"]').unbind('click')
                $('i[data-action="add-key-value-row"]').click(add_key_value_row)


                function add_key_value_row(e) {
                    modal.find('.modal-body').append(keyValueTpl)

                    $('i[data-action="remove-key-value-row"]').unbind('click')
                    $('i[data-action="remove-key-value-row"]').click(remove_key_value_row)

                    var v = $lxh.validator([
                        {name: 'category_name', rules: 'required', },
                        {name: 'key[]', rules: 'required', },
                        {name: 'value[]', rules: 'required' },
                    ], function () {

                    }, '.modal-container')
                    v._validateForm('submit')
                }

                function remove_key_value_row(e) {
                    $(e.currentTarget).parent().parent().remove()
                }

            },
            createOptions: function (e) {
                console.log('createOptions')
            },

            // 复制文件
            copyFile: function (e) {
                var path = this.$packageTitle.text()
                // 判断是否选择了文件
                if (path.indexOf('/') == -1) {
                    return this.notify.error(trans('Please select a file first', 'tip'))
                }

                this.modal('Target', $('#createFileTpl').text(), 'Save', function () {
                    var lang = $('input[name="lang_name"]').val(),
                        module =  $('input[name="module_name"]').val(),
                        file = $('input[name="filename"]').val()

                    if (! lang || ! module || ! file) {
                        return this.notify.error(trans('Invalid arguments', 'tip'))
                    }

                    if (file.indexOf('.') != -1) {
                        return this.notify.error(trans('Invalid arguments', 'tip'))
                    }

                    this.notify.info(trans('loading'))

                    var model = $lxh.createModel()
                    model.on('success', function (data) {
                        swal.close()

                        window.location.reload()

                    }.bind(this))

                    var newPath = lang + '/' + module + '/' + file + '.php'

                    model.data({newPath: newPath, path: path})

                    model.touchAction('CopyFile', 'POST')
                })
            },

            // 创建语言包
            createFile: function (e) {
                this.modal('Create File', $('#createFileTpl').text(), 'Create', function () {
                    var lang = $('input[name="lang_name"]').val(),
                        module =  $('input[name="module_name"]').val(),
                        file = $('input[name="filename"]').val()
                    if (! lang || ! module || ! file) {
                        return this.notify.error(trans('Invalid arguments', 'tip'))
                    }

                    if (file.indexOf('.') != -1) {
                        return this.notify.error(trans('Invalid arguments', 'tip'))
                    }

                    this.notify.info(trans('loading'))

                    var model = $lxh.createModel()
                    model.on('success', function (data) {
                        swal.close()

                        window.location.reload()

                    }.bind(this))

                    model.data({lang: lang, module: module, file: file})

                    model.touchAction('CreateFile', 'POST')
                })
            },

            // 文件树点击事件
            leafClick: function (e, data) {
                var $this = $(e.currentTarget),
                    // 父级文件夹字符串
                    parentString = $this.find('span').attr('data-parent'),
                    // 文件夹名或文件名
                    name = $this.text(),
                    // parent表示文件夹，sub表示文件
                    type = $this.find('span').hasClass('parent') ? 'parent' : 'sub'

                // 只允许点击文件
                if (type == 'parent') return

                var path = parentString + '/' + name
                path = path.replace('/', '')

                if (path == this.$packageTitle.text()) return

                this.notify.info(trans('loading'))

                this.$packageTitle.text(path)

                this.model.data({'content': path})

                // 获取语言包
                this.model.touchAction('GetPackage', 'POST')
            }
        },

        // 弹窗
        modal: function (title, text, confirmButtonText, call) {
            swal({
                title: trans(title), text: text, showCancelButton: true, confirmButtonClass: 'btn-success',
                confirmButtonText: trans(confirmButtonText), cancelButtonText: trans('Cancel'), closeOnConfirm: false, allowEscapeKey: true,
                showLoaderOnConfirm: true, animation: true, html: true, containerClass: 'col-md-4'
            }, call.bind(this))
        },

        // 渲染语言包到table列表
        renderLanguageList: function (data) {
            this.toggleTableInput(false)

            this.notify.remove()

            this.$languageBody.html(this.languageBlade.fetch({list: data.content}))

            this.$languageTable.find('i[data-action="remove-edit-row"]').unbind('click')
            this.$languageTable.find('i[data-action="remove-edit-row"]').click(this.events.removeRow.bind(this))
        }
    }

    add_action(function () {
        language.init()
    })
})
