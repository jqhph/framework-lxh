<html>
<link href="/assets/admin/packages/terminal-emulator/terminal.css?v=1521618870" rel="stylesheet" type="text/css">
<script src="/assets/admin/js/jquery.min.js?v=12345123"></script>
<script src="/assets/admin/packages/terminal-emulator/terminal.js?v=12345123"></script>
<body>
<div class="terminal-container"></div>


<script>
(function () {
    var test = 'sdfsd  dfdf 55656';

    test = test.split(' ');

    var _n = [];
    for (var i in test) {
        if (test[i]) {
            _n.push(test[i]);
        }
    }

    console.log(123, _n);

    var terminal = new Terminal({
        title: 'Lxh',
        messages: [
            {content: 'test', type: 'info'},
        ],
    });

    terminal.render();

    console.log(123123, terminal);
})();
</script>
</body>
</html>
