<?php
/**
 * 详情界面公共模板
 *
 * @author Jqh
 * @date   2017/8/1 09:28
 */

use Lxh\Admin\Kernel\Url;

$row = isset($row) ? $row : [];

$width = isset($width) ? $width : 6;

$id = get_value($row, 'id');

// 是否加载js
$loadJs = isset($loadJs) ? $loadJs : '';

?>
<div class="row">
    <div class="col-sm-12">
        <div class="card-box">
            <div class="card-box-header">
                <span class="card-box-title"><?php echo trans_with_global('Primary');?></span>
                <div class="pull-right"></div>
            </div>
            <div class="card-box-line m-b-30"></div>

            <div class="row">
                <form class="form-horizontal <?php echo __CONTROLLER__;?>-form" role="form">
                    <div class="col-lg-<?php echo $width;?>">
                        <?php if (! empty($row['id'])) { ?>
                            <input type="hidden" name="id" value="<?php echo $id;?>" />
                        <?php  }

                        foreach ($opts as & $v) {
                            $v['vars']['id'] = & $id;
                            $v['vars']['value'] = get_value($row, $v['vars']['name']);

                            echo render_view("component.fields/{$v['view']}", $v['vars']);
                        }

                        echo render_view('component.detail-button');?>
                    </div><!-- end col -->

                </form>

            </div><!-- end row -->
        </div>
    </div><!-- end col -->
</div>
<?php
// 加载js
if ($loadJs) { ?>
<script>add_js(parse_view_name('<?php echo __CONTROLLER__;?>', 'detail'));</script>
<?php }?>