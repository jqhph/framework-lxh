<p>
    <?php echo trans('Please provide the following information. Don&#8217;t worry, you can always change these settings later.');?>
</p>
<p>
    <?php echo sprintf(
        trans('The following tables will be created, and don&#8217;t worry, all tables are not required.You can delete some or all of the tables at will!<br>%1s'),
        '<code>admin, admin_trash, admin_login_log, abilities, roles, assigned_abilities, assigned_roles, menu, admin_operation_log</code>'
    )?>
</p>

<br>
<?php echo $form->render()?>