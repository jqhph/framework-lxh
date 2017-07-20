<?php echo component_view('fields/varchar/edit',
    ['name' => 'controller_name', 'label' => 'controller', 'value' => '']); ?>

<?php echo component_view('fields/varchar/edit',
    ['name' => 'en_name', 'label' => 'english name', 'value' => '']); ?>

<?php echo component_view('fields/varchar/edit',
    ['name' => 'zh_name', 'label' => 'chinese name', 'value' => '']); ?>

<?php echo component_view('fields/varchar/edit',
    ['name' => 'author', 'label' => 'author', 'value' => '']); ?>

<?php echo component_view('fields/enum/edit',
    ['name' => 'module', 'label' => 'module', 'list' => & $moduleOptions]); ?>

<?php echo component_view('fields/enum/edit',
    ['name' => 'inheritance', 'label' => 'inheritance of controller', 'list' => & $controllerOptions]); ?>

<?php echo component_view('fields/checkbox/edit',
    ['name' => 'actions', 'label' => 'actions', 'rows' => [
        ['label' => 'add', 'value' => 'add', 'checked' => 1],
        ['label' => 'update', 'value' => 'update', 'checked' => 1],
        ['label' => 'delete', 'value' => 'delete', 'checked' => 1],
        ['label' => 'list', 'value' => 'list', 'checked' => 1],
        ['label' => 'search', 'value' => 'search', 'checked' => 1],
        ['label' => 'order by', 'value' => 'order', 'checked' => 1],
        ['label' => 'display menu ', 'value' => 'displayMenu', 'checked' => 1],
    ]]); ?>

<?php echo component_view('fields/varchar/edit',
    ['name' => 'icon', 'label' => 'icon', 'value' => '']); ?>

<?php echo component_view('fields/varchar/edit',
    ['name' => 'limit', 'label' => 'page limit', 'value' => 20]); ?>