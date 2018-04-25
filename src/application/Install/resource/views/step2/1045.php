<p>
    <?php echo sprintf(
        trans('This either means that the username and password information is incorrect or we can&#8217;t contact the database server at <code>%1$s</code>. This could mean your host&#8217;s database server is down.'),
        $config['host']
    );?>
</p>
<ul>
    <li><?php echo  trans('Are you sure you have the correct username and password?');?></li>
    <li><?php echo trans('Are you sure that you have typed the correct hostname?')?></li>
    <li><?php echo trans('Are you sure that the database server is running?')?></li>
</ul>
<br>
<a onclick="history.go(-1)" class="btn btn-primary"> <?php echo trans('Retry')?> </a>