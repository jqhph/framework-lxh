/**
 * Created by Jqh on 2017/7/21.
 */
define([], function () {
    window.lxh_action = function () {

        $('.basic-language').jstree({
            'core' : {
                // 'data': languageCatelog,
                'themes' : {
                    'responsive': true
                },
                'check_callback':
                    function (operation, node, parent, position, more) {
                    console.log('operation', operation)
                        console.log('node', node)
                        console.log('parent', parent)
                        console.log('position', position)
                        console.log('more', more)
                }
            },
            'types' : {
                'default' : {
                    'icon' : 'zmdi zmdi-folder-star folder',
                },
                'file' : {
                    'icon' : 'zmdi zmdi-file file'
                }
            },
            'plugins' : ['types', 'wholerow', 'sort', 'contextmenu', 'ui']
        });

        $('a.jstree-anchor').on("click.jstree", function (e, data) {
            var $this = $(e.currentTarget),
                // 父级文件夹字符串
                parentString = $this.find('span').attr('data-parent'),
                // 文件夹名或文件名
                name = $this.text(),
                // parent表示文件夹，sub表示文件
                type = $this.find('span').hasClass('parent') ? 'parent' : 'sub'

            if (type == 'parent') return;



        });

    }

})