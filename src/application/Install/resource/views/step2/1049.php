<p>
    <?php echo sprintf(
        trans('We were able to connect to the database server (which means your username and password is okay) but not able to select the %s database.'),
        $config['name']
    );?>
</p>
<ul>
    <li><?php echo  trans('Are you sure it exists?');?></li>
    <li><?php echo sprintf(
            trans('Does the user %1$s have permission to use the %2$s database?'),
            $config['user'],
            $config['name']
        )?></li>
    <li><?php echo sprintf(
            trans('On some systems the name of your database is prefixed with your username, so it would be like <code>username_%1$s</code>. Could that be the problem?'),
            $config['name']
        )?></li>
</ul>
<br>
<a onclick="history.go(-1)" class="btn btn-primary"> <?php echo trans('Retry')?> </a>