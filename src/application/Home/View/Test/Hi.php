<html>
    <body>
        <p>
            dsfsdfsd<pre><?php print_r($data)?>
        </p>

    <form action="/Test/Hi" enctype="multipart/form-data" method="post">
        <div>
            <a id="addAttach" href="#">添加上传文件</a>
            <div id="files">
                <input type="file" name="f1"/>
                <input type="file" name="f2"/>
                <input type="hidden" name="act" value="upload"/>
            </div>
            <input type="submit" value="上传"/>
        </div>

    </form>

    </body>
<script>

</script>

</html>
