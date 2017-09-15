<?php use Lxh\Admin\Kernel\Url;?>
<?php echo render_view('component.fields/varchar/edit',
    ['name' => 'controller_name', 'label' => 'controller', 'value' => '']); ?>

<?php echo render_view('component.fields/varchar/edit',
    ['name' => 'en_name', 'label' => 'english name', 'value' => '']); ?>

<?php echo render_view('component.fields/varchar/edit',
    ['name' => 'zh_name', 'label' => 'chinese name', 'value' => '']); ?>

<?php echo render_view('component.fields/varchar/edit',
    ['name' => 'author', 'label' => 'author', 'value' => '']); ?>

<?php echo render_view('component.fields/enum/edit',
    ['name' => 'inheritance', 'label' => 'inheritance of controller', 'opts' => & $controllerOptions]); ?>

<?php echo render_view('component.fields/checkbox/edit',
    ['name' => 'actions', 'opts' => [
        ['label' => 'add', 'value' => 'add', 'checked' => 1],
        ['label' => 'update', 'value' => 'update', 'checked' => 1],
        ['label' => 'delete', 'value' => 'delete', 'checked' => 1],
        ['label' => 'list', 'value' => 'list', 'checked' => 1],
        ['label' => 'search', 'value' => 'search', 'checked' => 1],
        ['label' => 'order by', 'value' => 'order', 'checked' => 1],
        ['label' => 'display menu ', 'value' => 'displayMenu', 'checked' => 1],
    ]]); ?>

<?php echo render_view('component.fields/varchar/edit',
    ['name' => 'icon', 'label' => 'icon', 'value' => '', 'help' => '<a target="_blank" href="' . Url::makeAction('font-awesome', 'public-entrance')
        . '">' . trans_with_global('fontawesome icon CSS') . '</a>']); ?>

<?php echo render_view('component.fields/varchar/edit',
    ['name' => 'limit', 'label' => 'page limit', 'value' => 20]); ?>