<html>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
<link href="/assets/admin/packages/terminal-emulator/terminal.css?v=1521618870" rel="stylesheet" type="text/css">
<script src="/assets/admin/js/jquery.min.js?v=12345123"></script>
<script src="/assets/admin/packages/terminal-emulator/terminal.js?v=12345123"></script>
<body>
<div class="terminal-container" style=""></div>

<script>
(function () {
    var terminal = $('.terminal-container').lxhTerminal({
        messages: [
            {content: '欢迎使用 Lxh terminal _(:з」∠)_'},
            {content: '请容俺介绍一下自己 (´•灬•‘) '},
            {content: '江清华', style: 'primary', label: '姓名：'},
            {content: (new Date().getFullYear() - 1993), style: 'primary', label: '年龄：'},
            {content: 'PHP程序员', style: 'primary', label: '职业：'},
            {content: '广州', style: 'primary', label: '坐标：'},
            {content: '841324345@qq.com', style: 'primary', label: '联系方式：'},
            {content: {title: '', list: ['完善开源项目', '学习Python', '学习算法', '多运动']}, style: 'primary', label: '2018 计划：'},
            {content: 'Over!(๑> 灬 <)', style: 'success', label: 'Done'}
        ]
    });

    console.log(1, terminal);
})();
</script>
</body>
</html>
