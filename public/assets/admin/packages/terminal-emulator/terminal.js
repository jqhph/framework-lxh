(function (w) {
    var translator,
        lastLineClass = '.terminal-last-line',
        windowClass = '.terminal-window',
        helpText = 'Type "help" to get a supporting command list.',
        unkownText = 'Unknown Command.',
        success = 'success',
        info = 'info',
        system = 'system',
        error = 'error',
        warning = 'warning';

    function Terminal(options) {
        var _t = this,
            def = {
                title: 'Lxh Terminal',
                locale: 'en',
                langs: {en: {}, 'zh-cn': {}},
                welcome: [
                    {content: '', type: system}
                ],
                messagesEnd: [
                    {content: helpText, type: system}
                ],
                messages: [],
                commands: {},
                tasks: [],
                element: '.terminal-container',
                loadingTime: 500,
                width: '90%',
            },
            defCommands = {
                help: {
                    handle: function () {
                        var commands = _t.commands(), i, content = '', builder = _t.builder;

                        for (i in commands) {
                            if (i == 'help') continue;
                            content += _t.builder.line('---> ' + translator.trans(commands[i].description), success, i)
                        }

                        return builder.row(builder.cmd(translator.trans('Here is a list of supporting command.'))) + content;
                    }
                },
                author: {
                    description: 'About author.',
                    handle: [
                        {content: 'Jiang QingHua.', type: info, label: 'Name:'},
                        {content: 'Under Construction.', type: info, label: 'Website:'},
                        {content: '841324345@qq.com', type: info, label: 'Email:'},
                        {content: '<a href="https://github.com/jqhph" target="_blank">https://github.com/jqhph</a>', type: info, label: 'Github:' },
                        {content: '841324345', type: info, label: 'QQ:'}
                    ],
                },
                readme: {
                    description: 'About this project.',
                    handle: [
                        { content: 'This is a component that emulates a command terminal in JQuery' }
                    ]
                },
                document: {
                    description: 'Document of this project.',
                    handle: [
                        {content: 'Under Construction.', type: error}
                    ]
                }
            };

        /**
         * 初始化操作
         */
        function init() {
            options = options || {};

            // 合并数组
            options = merge(def, options);
            options.commands = merge(defCommands, options.commands);

            this.$el = $(this.element());

            this.translator = translator;

            // 翻译器
            translator.set(options.langs[options.locale]);

            this.builder = new Builder(this);

            var supportCommands = {};
            for (var i in options.commands) {
                supportCommands[i] = 1;
            }
            this.supportCommands = supportCommands;
        }

        /**
         * 
         * @returns {*|string}
         */
        this.element = function () {
            return options.element || def.element;
        };
        
        /**
         * 消息
         *
         * @returns {Array|Terminal.messages|*|$.validator.defaults.messages|{}|defaults.messages}
         */
         this.messages = function () {
             return options.messages;
         };

        /**
         * 获取命令数据
         *
         * @returns {*}
         */
        this.commands = function () {
            return options.commands;
        };

        /**
         * 任务
         *
         * @returns {*}
         */
        this.tasks = function () {
            return options.tasks;
        };

        /**
         * 获取配置
         *
         * @param key
         * @param def
         * @returns {*}
         */
        this.option = function (key, def) {
            if (! key) return options;
            return options[key] || def;
        };

        function merge(_old, _new) {
            for (var i in _old) {
                if (typeof _new[i] == 'undefined') {
                    _new[i] = _old[i];
                }
            }

            return _new;
        }

        // 初始化
        init.call(this);
    }

    Terminal.prototype = {
        /**
         * 渲染terminal界面
         */
        render: function () {
            var headerStart = '<div class="terminal"><div style="position:relative">',
                footerEnd = '</div></div>',
                bodyStart = '<div class="terminal-w-c" style="position:absolute;top:0;left:0;right:0;overflow:auto;z-index:1;margin-top:30px;max-height:500px" ref="terminalWindow"><div class="terminal-window" >',
                bodyEnd = '</div></div>',
                _t = this;

            this.html = headerStart
                + this.builder.header()
                + bodyStart
                + this.builder.body()
                + bodyEnd
                + footerEnd;

            this.$el.html(this.html);
            this.bind();

            setTimeout(function () {
                var deg = 'rotate(720deg)';
                _t.$el.find('.terminal').css({
                    'transform': deg,
                    '-webkit-transform': deg,
                    '-moz-transform': deg,
                    '-o-transform': deg,
                    '-ms-transform': deg,
                    'width': _t.option('width')
                });
            },2);

            function render_rows(rows, next, useTime) {
                var message = rows.shift();

                if (message) {
                    _t.loading(render);
                } else {
                    next();
                }

                function render() {
                    _t.append(
                        _t.builder.line(translator.trans(message.content), message.type, message.label, useTime)
                    );

                    if (message = rows.shift()) {
                        _t.loading(render);
                    } else {
                        next(true);
                    }
                }
            }

            render_rows(_t.messages(), function () {
                render_rows(_t.option('messagesEnd'), function (has) {
                    if (has) {
                        _t.loading(function (_t) {
                            _t.append(_t.builder.lastLine());
                        });
                    } else {
                        _t.append(_t.builder.lastLine());
                    }
                });
            }, true);

        },

        /**
         * loading效果
         *
         */
        loading: function (callback) {
            var _t = this, builder = this.builder, text = '...', counter = 0;
            _t.append(
                builder.row(
                    builder.span('loading', text)
                )
            );
            setTimeout(function () {
                _t.$el.find('.loading').remove();
                callback && callback(_t);
            }, _t.option('loadingTime'));
        },

        bind: function () {
            var events = {
                // 光标选中以及移动到最后
                focus: function (e) {
                    var terminal = this, $input = terminal.$el.find('.input-box'),
                        $last = $(e.currentTarget).find(lastLineClass);

                    $input.focus();
                    $input.off('click').click(function (obj) {
                        var _t = this;
                        $(_t).focus();
                        setTimeout(function () {
                            var len = 1000;
                            if (document.selection) {
                                var sel = _t.createTextRange();
                                sel.moveStart('character', len);
                                sel.collapse();
                                sel.select();
                            } else if (typeof _t.selectionStart == 'number'
                                && typeof _t.selectionEnd == 'number') {
                                _t.selectionStart = _t.selectionEnd = len;
                            }
                        }, 30);
                    });
                    $input.off('keyup').on('keyup', function (e) {
                        var val = this.value;
                        if (e.keyCode != 13) {
                            // 显示内容
                            return $(this).parent().find('.content').html(val);
                        }

                        // 输出内容
                        $last.remove();
                        terminal.input(val);
                    });
                }
            };
            ///////////////////////////////////////////////////
            this.$win = $(windowClass);

            // 光标选中以及移动到最后
            this.$el.off('click').on('click', events.focus.bind(this)).click();
        },

        /**
         * 追加内容
         *
         * @param content
         */
        append: function (content) {
            this.$win.append(content);
            this.bind();
        },

        /**
         * 输入内容
         *
         * @param content
         */
        input: function (content) {
            var builder = this.builder;
            content = this.run(content);

            if (content !== false) {
                content = builder.line(content);
            } else {
                content = ''
            }

            this.append(
                content + builder.lastLine()
            );
        },

        /**
         * 运行命令
         *
         * @param input
         */
        run: function (input) {
            var inputs = input.split(' ');
            var _n = [], command, i, builder = this.builder, commands = this.commands();
            for (i in inputs) {
                if (inputs[i]) {
                    _n.push(inputs[i]);
                }
            }

            command = _n.shift();
            if (! command) {
                return '';
            }

            if (typeof this.supportCommands[command] == 'undefined') {
                this.append(
                    builder.line(input) +
                    builder.line(translator.trans(unkownText), system) +
                    builder.line(translator.trans(helpText), system)
                );

                return false;
            }

            var res = builder.line(command);
            command = commands[command];

            switch (typeof command.handle) {
                case 'undefined':
                    return res;
                case 'object':
                    return res + build_lines(command.handle);
                case 'function':
                    return res + build_lines(command.handle());
                    break;
                default:
                    return res + command.handle;
            }

            function build_lines(rows) {
                if (typeof rows != 'object') return rows;
                var contents = '';
                for (i in rows) {
                    contents += builder.systemline(rows[i].content, rows[i].type, rows[i].label);
                }
                return contents;
            }
        }
    };

    /**
     * 翻译器
     *
     * @constructor
     */
    function Translator() {
        var packages = {};

        /**
         * 初始化操作
         */
        function init() {
            packages = packages || {};
        }

        /**
         * 获取语言包
         *
         * @param key
         * @returns {*}
         */
        this.get = function (key) {
            return packages[key] || key;
        };

        /**
         *
         * @param _packages
         */
        this.set = function (_packages) {
            packages = _packages || packages;
        };

        // 初始化
        init.call(this);
    }

    Translator.prototype = {
        trans: function (text, replaces) {
            replaces = replaces && typeof replaces.splice == 'function' ? replaces : [replaces];

            var i = -1;
            return this.get(text).replace(/%s/ig, function ($match, position) {
                i++;
                return replaces[i] || ''
            });
        }
    };

    function Builder(terminal) {
        var title = terminal.option('title');

        return {
            header: function () {
                return '<div class="header"><h4>' + title + '</h4><ul class="shell-dots"><li class="red"></li><li class="yellow"></li><li class="green"></li></ul></div>';
            },

            body: function () {
                var text = translator.trans('Welcome to %s.', title);

                return this.row(text)
                    + this.row(
                        this.prompt() + this.cmd('cd ' + title)
                    );
            },

            // 最后一行
            lastLine: function () {
                return this.row(function (builder) {
                    return builder.prompt(title+'/ ') + builder.span('content') + builder.span('cursor', '&nbsp;') + builder.input();
                }, lastLineClass.replace('.', ''));
            },

            /**
             * 渲染列表
             *
             * @param list
             */
            list: function (list, title) {
                if (!list || typeof list.push == 'undefined') {
                    return list;
                }

                title = title || '';
                var lis = '', i;

                for (i in list) {
                    lis += '<li><pre>' + list[i] + '</pre></li>';
                }

                if (title) {
                    title = this.span('', title);
                }

                return title + '<ul>' + lis + '</ul>';
            },

            /**
             * 显示一行数据
             *
             * @param content 要输出的内容
             * @param type  success info warning error system
             * @param label
             * @returns {*}
             */
            line: function (content, type, label, useTime) {
                var prompt = '';
                if (! type && ! label) {
                    prompt = this.prompt() + ' ';
                }

                if (useTime) prompt = time() + ' ' + prompt;

                if (type && !label) label = type;

                return this.row(
                    prompt + this.span(type, label) + ' ' + this.cmd(content)
                );
            },

            systemline: function (content, type, label) {
                if (type && !label) label = type;

                return this.row(
                    this.span(type, label) + ' ' + this.cmd(content)
                );
            },

            input: function () {
                return '<input type="text" class="input-box" />';
            },

            row: function (content, cls) {
                return '<p class="' + (cls||'') + '">' + value.call(this, content) + '</p>';
            },

            prompt: function (content) {
                return this.span('prompt', content);
            },

            cmd: function (content) {
                if (typeof content == 'object' && typeof content.list == 'object') {
                    if (typeof content.list.push != 'undefined') {
                        content = this.list(content.list, content.title);
                    }
                }

                return this.span('cmd', content);
            },

            success: function (content) {
                return this.span(success, content);
            },

            info: function (content) {
                return this.span(info, content);
            },

            warning: function (content) {
                return this.span(warning, content);
            },

            error: function (content) {
                return this.span(error, content);
            },

            system: function (content) {
                return this.span(system, content);
            },

            span: function (cls, content) {
                return '<span class="'+ (cls || '') +'">'+translator.trans(value.call(this, content))+'</span>'
            }

        };
    }

    function value(value) {
        if (typeof value == 'function') {
            return value(this);
        }
        return value || '';
    }

    function time() {
        return new Date().toLocaleTimeString().split('').splice(2).join('')
    }


    function init() {
        translator = new Translator();

        w.Terminal = Terminal;
    }

    init();
   
})(window);