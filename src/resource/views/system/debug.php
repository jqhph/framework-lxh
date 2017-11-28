<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ERROR</title>

    <style type="text/css">
        ::selection { background-color: #E13300; color: white; }
        ::-moz-selection { background-color: #E13300; color: white; }

        body {
            background-color: #fff;
            margin: 40px;
            font: 13px/20px normal Helvetica, Arial, sans-serif;
            color: #4F5155;
        }

        a {
            color: #003399;
            background-color: transparent;
            font-weight: normal;
        }

        h1 {
            color: #444;
            background-color: transparent;
            border-bottom: 1px solid #D0D0D0;
            font-size: 19px;
            font-weight: normal;
            margin: 0 0 14px 0;
            padding: 14px 15px 10px 15px;
        }

        code {
            font-family: Consolas, Monaco, Courier New, Courier, monospace;
            font-size: 14px;
            background-color: #f9f9f9;
            border: 1px solid #D0D0D0;
            color: #002166;
            display: block;
            margin: 16px 0 16px 0;
            padding: 12px 10px 12px 10px;
        }

        #body {
            margin: 0 15px 0 15px;
        }

        p.footer {
            text-align: right;
            font-size: 12px;
            border-top: 1px solid #D0D0D0;
            line-height: 32px;
            padding: 0 10px 0 10px;
            margin: 20px 0 0 0;
        }

        #container {
            margin: 0 auto;max-width: 1200px;
            border: 1px solid #D0D0D0;
            box-shadow: 0 0 8px #D0D0D0;
        }
        #container p span{font-size: 15px}
        .number, .danger{color: #a94442}

    </style>
</head>
<body>

<div id="container">
    <h1 class="danger">ERROR: </h1>

    <div id="body">
        <p><b>MSG:</b>  <span class="danger"><?php echo $msg;?></span></p>

        <p><b>CODE:</b> <span class="danger"><?php echo $code;?></span></p>

        <p><b>FILE:</b> <span class="danger"><?php echo $file;?>(<?php echo $line;?>)</span></p>

        <?php echo isset($preview) ? $preview : '';?>
        <p></p>
        <code><?php echo str_replace("\n", '<br/>', $trace);?></code>
    </div>

    <p class="footer"></p>
</div>

</body>
</html>