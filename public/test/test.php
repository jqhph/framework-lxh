<html>
<link href="/assets/admin/packages/terminal-emulator/terminal.css?v=1521618870" rel="stylesheet" type="text/css">
<script src="/assets/admin/js/jquery.min.js?v=12345123"></script>
<script src="/assets/admin/packages/terminal-emulator/terminal.js?v=12345123"></script>
<body>
<div class="terminal-container" style="width:60%;margin:0 auto"></div>


<script>
(function () {
    var terminal = $('.terminal-container').lxhTerminal({
        
    });

    console.log(1, terminal);
})();
</script>
</body>
</html>
