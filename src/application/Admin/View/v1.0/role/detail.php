<?php
    echo render_view('component.detail.detail', [
        'row' => & $row, 'items' => & $items, 'width' => 12, 'loadJs' => $loadJs, 'validatorRules' => & $validatorRules
    ]);
?>
