<!--col-sm-12-->
<div class="<?php echo $args->topClass; ?>">
    <div class="card-box">

        <div class="table-rep-plugin">
            <div class="table-responsive" data-pattern="priority-columns">
                <table id="tech-companies-1" class="table  table-striped">
                    <thead>
                    <tr>
                        <?php foreach ($args->titles as $k => & $v) {?>
                        <th data-priority="<?php echo $k;?>"><?php echo trans($v, 'fields'); ?></th>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    foreach ($args->list as $k => & $r) {?>
                    <tr>
                        <?php foreach ($r as $name => & $v) { ?>
                        <th><?php
                            if (! empty($args->fieldsType[$name])) {
                                echo component_view($args->fieldsType[$name], ['val' => $v]);
                            } else {
                                echo $v;
                            }
                            ?>
                        </th>
                        <?php } ?>
                    </tr>
                    <?php } ?>

                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>
<?php

if ($args->useRwd) {
    load_css('rwd-table.min', 'lib/plugins/RWD-Table-Patterns/dist/css');
    load_js('rwd-table', 'plugins/RWD-Table-Patterns/dist/js');
}

?>