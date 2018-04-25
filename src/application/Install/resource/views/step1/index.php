<p>
    <?php echo trans(
        'Welcome to Lxh Framework. Before getting started, You will need to know the following items before proceeding:'
    )?>
</p>
<ol>
    <li><?php echo trans('Database name')?></li>
    <li><?php echo trans('Database username')?></li>
    <li><?php echo trans('Database password')?></li>
    <li><?php echo trans('Database host')?></li>
</ol>
<p><?php
    echo sprintf(
        trans('We&#8217;re going to use this information to create a %s file.'),
        '<code>'.str_replace('\\', '/', __CONFIG__).'dev/database.php</code>'
    );
    ?>
    <strong>
        <?php
        echo sprintf(
            trans('If for any reason this automatic file creation doesn&#8217;t work, don&#8217;t worry. All this does is fill in the database information to a configuration file. You may also simply open %1$s in a text editor, fill in your information.'),
            '<code>'.str_replace('\\', '/', __CONFIG__).'dev/database.php</code>'
        );
        ?>
    </strong>
    <?php echo sprintf(
        trans('Need more help? <a target="_blank" href="%s">We got it</a>.'),
        $helper
    );?>
</p>
<br>
<p>
    <a class="btn btn-primary" href="/install/2">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo trans('Next')?>&nbsp;&nbsp;&nbsp;&nbsp; </a>
</p>