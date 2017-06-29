/**
 * Created by Jqh on 2017/6/22.
 */
function Blade(tpl, vars) {
    var store = {
        // 渲染节点
        selector: '#blade',
        // 渲染成功后的回调函数
        call: null,
        // 模板内容
        tpl: tpl,
        // 需要编译的变量
        vars: {},
        // 变量分隔符
        delimiter: {start: '{', end: '}'},
        // 模板占位符
        placeholders: {
            $if: '@if',
            $else: '@else',
            $elseif: '@elseif',
            $endif: '@endif',
            $foreach: '@foreach',
            $endforeach: '@endforeach',
            $end: '@',
            $tags: {
                compare: '@compare'
            }
        },
        comparison: {
            // '&gt;': '>', '&lt;': '<', '&amp;': '&'
        },
        regs: {
            // 普通变量，精确匹配 var: /{([a-z|_]*[0-9]*[_]*[\.]*([a-z|_]+[0-9]*[_]*)+([\[]?[a-z0-9_]*[\]]?))}/gi,
            $var: /{([a-z|_]*[0-9]*[_]*[\.]*([a-z|_]*[0-9]*[_]*)+([\[]?([a-z|_]*[0-9]*[_]*[\.]*([a-z|_]*[0-9]*[_]*)+)[\]]?))}/gi,
            stringVar: /[\[]([a-z|_]*[0-9]*[_]*[\.]*([a-z|_]+[0-9]*[_]*)+)[\]]/gi,
            // js变量（非模板变量）
            jsvar: /((?![\'|\"])[\[_\.\]a-z]+[0-9\]]*)+[ ]*/gi,
            $if: /@if[ ]+([\s\S.])+@endif\b/gi,
            $foreach: /@foreach[ ]+([\s\S.])+@endforeach\b/gi,
            // 标签，精确匹配
            tag: /{(@[^{|{#]+)}/gi,
            tagName: /@[a-z]*\b/i
        },
        model: {
            $if: "\n if ({exp}) { \n {content} \n } \n",
            $elseif: " else if ({exp}) { \n {content} \n } \n",
            $else: " else { \n {content} \n } \n"
        }
    }

    store.tpl = tpl
    store.vars = vars || {}
    var self = this

    this.getTpl = function () {
        return store.tpl
    }

    // 解析并获取模板内容
    this.fetch = function (vars) {
        store.vars = vars || store.vars
        var tpl = store.tpl
        // 语法检测
        syntax_analysis(tpl);
        // 模板表达式解析
        var tpl = parse_expression(tpl)
        // 普通变量解析
        return parse_var(tpl)
    };

    // 渲染模板
    this.render = function (selector, callback) {
        store.selector = selector || store.selector
        store.call = callback || store.call

        document.querySelector(store.selector).innerHTML = this.fetch()

        typeof store.call != 'function' || store.call(this)
    }

    // 重新渲染模板
    this.rerender = function (vars) {
        store.vars = vars || store.vars
        this.render()
    }

    // 分配变量到模板
    this.assign = function (key, value) {
        if (typeof key == 'object') {
            store.vars = key
        } else {
            if (typeof value == 'object') {
                store.vars[key] = value
            } else {
                store.vars[key] = value
            }
        }
    }

    // 模板表达式解析
    var parse_expression = function (tpl) {
        // foreach
        tpl = parse_expression_foreach(tpl)
        // 标签解析
        tpl = parse_tag(tpl)
        // if
        tpl = parse_expression_if(tpl)
        // values = values.replace(/[\s\n]/gi, "")
        return (tpl != 'undefined' ? tpl : '')
    }

    // 标签解析
    var parse_tag = function (tpl) {
        return tpl.replace(store.regs.tag, function (full, $match, position) {
            var tagName = $match.match(store.regs.tagName)
            tagName = tagName[0]
            for (var i in store.placeholders.$tags) {
                if (tagName == store.placeholders.$tags[i]) {
                    return compile_tags[i](comparison_replace($match.replace(tagName, "")))
                }
            }
            return full
        })
    }

    // 解析标签
    var compile_tags = {
        compare: function (tpl) {
            var tagContent = this.compareSyntaxAnalysis(tpl, '??', 2)
            var content = this.compareSyntaxAnalysis(tagContent[1], '::', 1)

            var exp = '{'
            for (var i in content) {
                if (typeof content[i] == 'function') continue
                if (i == 1) exp += '} else {'
                if (content[i].indexOf("'") != -1 || content[i].indexOf('"') != -1 ) {
                    exp += ' var result = ' + content[i]
                } else {
                    exp += ' var result = ' + this.transVar(content[i])
                }
            }
            exp = 'if (' + this.transVar(tagContent[0]) + ') ' + exp + '}'
            eval(exp)
            return result || ''

        },
        // 翻译变量
        transVar: function (tpl) {
            return tpl.replace(store.regs.jsvar, function (full, $match) {
                return trans_var($match, null, true)
            })
        },
        compareSyntaxAnalysis: function (content, type, length) {
            content = content.split(type)
            if (content.length < length) {
                throw new Error('Syntax error: "' + store.placeholders.$tags.compare + '" expression is not legal')
            }
            return content
        }
    }

    var parse_expression_foreach = function (tpl, test) {
        return tpl.replace(store.regs.$foreach, function (full, $match, position) {
            var results = get_exp_and_content('foreach', 'endforeach', full, true, true)
            var foreachTpl = ''
            var tmpExps = results.exp.split(' '), exps = []
            for (var i = 0; i < tmpExps.length; i++) {
                if (!tmpExps[i] || tmpExps[i] == "\n")  continue
                exps.push(tmpExps[i].replace(/[\s\n]/gi, ""))
            }

            var content = '', list = [], key = null, value, i
            // 获取循环list数组
            list = exps.shift()
            list = get_var_name(list)
            list = get_var(list) || []
            // 循环key和value变量赋值
            if (exps.length > 1) {
                key = exps.shift()
            }
            value = exps.pop()

            for (i in list) {
                if (typeof list[i] == 'function') {
                    continue
                }
                foreachTpl = results.content
                if (key) {
                    self.assign(get_var_name(key), i)
                }
                self.assign(get_var_name(value), list[i])

                // 标签解析
                foreachTpl = parse_tag(foreachTpl)

                if (has('foreach', foreachTpl)) {
                    foreachTpl = parse_expression_foreach(foreachTpl, 'test')
                }

                // 解析foreach循环里面的if表达式
                content += parse_expression_if(foreachTpl)

                // 解析变量
                content = parse_var(content)
            }
            return content
        })
    }

    var parse_expression_if = function (tpl, notParseForeach) {
        return tpl.replace(store.regs.$if, function (full, $match, position) {
            var ifContent = get_exp_and_content('if', 'end', full)

            full = ifContent.full

            var allContents = []
            allContents.push(ifContent.content)
            // 解析if
            var evalString = get_eval_string('if', ifContent, 0)

            // 计算elseif出现次数
            var elseifTimes = get_length(store.placeholders.$elseif, full)

            // 解析elseif
            for (var i = 1; i <= elseifTimes; i++) {
                var tmp = get_exp_and_content('elseif', 'end', full)
                full = tmp.full

                allContents.push(tmp.content)

                evalString += get_eval_string('elseif', tmp, i)
            }

            var elseTimes = get_length(store.placeholders.$else, full)

            // 解析else
            if (elseTimes) {
                tmp = get_exp_and_content('else', 'end', full)

                if (tmp.full) {
                    full = tmp.full.replace(store.placeholders.$else, "")
                }

                allContents.push(tmp.content)

                evalString += get_eval_string('else', tmp, i)
            }

            eval(evalString)
            if (full) {
                full = full.replace(store.placeholders.$endif, "")
            }

            return (parse_var(allContents[key]) || '') + full
        })
    }

    // 解析变量
    var parse_var = function (tpl, vars, toString, $default) {
        if (!tpl) return null
        return tpl.replace(store.regs.$var, function (full, $match, position) {
            return trans_var($match, vars, toString, $default)
        })
    }

    var trans_var = function ($var, vars, toString, $default) {
        if ($var.indexOf('[') == -1) {
            if (toString) {
                return '"' + get_var($var, vars, $default) + '"'
            }
            return get_var($var, vars, $default)
        } else {
            return get_var($var.replace(store.regs.stringVar, function (f, $m) {
                return '.' + get_var($m, vars, $default)
            }), $default)
        }
    }

    // 解析变量
    var get_var = function ($key, vars, $default) {
        var $lastItem = vars || store.vars, keys = $key.split('.')
        for (var i = 0; i < keys.length; i++) {
            if (typeof $lastItem[keys[i]] != 'undefined') {
                $lastItem = $lastItem[keys[i]]
            } else {
                return (typeof $default != 'undefined') ? $default : store.delimiter.start + $key + store.delimiter.end
            }
        }
        return $lastItem;
    }

    // 语法检测
    function syntax_analysis(tpl) {
        var msg = 'Syntax error: '
        // 计算if次数
        var ifTimes = get_length(store.placeholders.$if, tpl), endifTimes = get_length(store.placeholders.$endif, tpl)
        if (ifTimes > endifTimes) {
            throw new Error(msg + 'miss "' + store.placeholders.$endif + '"')
        } else if (ifTimes < endifTimes) {
            throw new Error(msg + 'miss "' + store.placeholders.$if + '"')
        }
        // 计算else次数
        var elseTimes = get_length(store.placeholders.$else, tpl)
        if (elseTimes > ifTimes) {
            throw new Error(msg + 'redundant "' + store.placeholders.$else + '" placeholder')
        }
        var elseifTimes = get_length(store.placeholders.$elseif, tpl)
        if (ifTimes == 0 && elseifTimes > 0) {
            throw new Error(msg + 'redundant "' + store.placeholders.$elseif + '" placeholder')
        }
        // 计算foreach
        var foreachTimes = get_length(store.placeholders.$foreach, tpl)
        var endforeachTimes = get_length(store.placeholders.$endforeach, tpl)
        if (foreachTimes > endforeachTimes) {
            throw new Error(msg + 'miss "' + store.placeholders.$endforeach + '"')
        } else if (foreachTimes < endforeachTimes) {
            throw new Error(msg + 'miss "' + store.placeholders.$foreach + '"')
        }
    }

    // 或取变量名称
    function get_var_name($key) {
        return $key.replace(new RegExp('[' + store.delimiter.start + '|' + store.delimiter.end + ']', 'gi'), "")
    }

    function get_eval_string(type, tmp, i) {
        var exp = parse_var(tmp.exp, null, true, '')

        exp = comparison_replace(exp)
        var str = store.model['$' + type].replace('{exp}', exp)
        return str.replace('{content}', 'var key = "' + i + '"')
    }

    // 表达式特殊字符串转换
    function comparison_replace(exp) {
        // for (var k in store.comparison) {
        //     exp = exp.replace(new RegExp(k, 'gi'), store.comparison[k])
        // }
        return exp
    }

    // 判断模板是否含有表达式标签
    function has(type, content) {
        if (content.indexOf(store.placeholders['$' + type]) != -1 && content.indexOf(store.placeholders['$end' + type]) != -1) {
            return true
        }
        return false
    }

    function get_exp_and_content(startType, endType, values, getAll, unset) {
        var start = store.placeholders['$' + startType], end = store.placeholders['$' + endType]

        var exp = values.match(new RegExp(start + '[ ]*([^@]*?)\n', 'i'))
        if (!exp || !exp[0]) {
            throw new Error('Syntax error: "' + start + '" miss expression content')
        }

        // 去除if 表达式部分
        values = values.replace(exp[0], "")

        if (unset) {
            if (end != '@') {
                var index = values.lastIndexOf(end)
                if (index != -1) {
                    values = values.substr(0, index)
                } else {
                    values = values.replace(end, "")
                }
            }
        }

        // 去掉@if头和换行，获得表达式条件
        exp = exp[0].replace(new RegExp('(?:' + start + '[ ])*|\n', 'gi'), '')

        // 检测是否存在嵌套if表达式标签
        if (has(startType, values)) {
            if (startType == 'if') {
                values = parse_expression_if(values, 'test')
            }
        }
        // 获取if表达式主体内容
        var content = ''
        if (getAll) {
            content = values
        } else {
            content = values.match(new RegExp('([^' + end + ']*)', 'gi')) // /([^@]*)/gi  ((?!@).*)
            content = content[0]
        }

        // 去除if主体内容
        values = values.replace(content, "")

        return {exp: exp, content: content, full: values}
    }

    // 计算表达式出现次数
    function get_length(type, tpl) {
        var t = tpl.match(new RegExp(type + '[\s]*', 'gi'))
        if (!t) {
            return 0
        }
        return t.length
    }
}