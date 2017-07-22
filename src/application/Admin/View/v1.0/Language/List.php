<div class="col-md-2">
    <div class="card-box">
        <div class="dropdown pull-right">
            <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">
                <i class="zmdi zmdi-more-vert"></i>
            </a>
            <ul class="dropdown-menu" role="menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li class="divider"></li>
                <li><a href="#">Separated link</a></li>
            </ul>
        </div>

        <h4 class="header-title m-t-0 m-b-30"><?php echo trans('Catalog'); ?></h4>

        <?php echo component_view('tree/basic', ['class' => 'basic-language', 'list' => & $list]); ?>

    </div>
</div>

<div class="col-md-10">
    <div class="card-box">
        <div class="dropdown pull-right">
            <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">
                <i class="zmdi zmdi-more-vert"></i>
            </a>
            <ul class="dropdown-menu" role="menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li class="divider"></li>
                <li><a href="#">Separated link</a></li>
            </ul>
        </div>

        <h4 class="header-title m-t-0 m-b-30"><?php echo trans('Details'); ?></h4>


    </div>
</div>

<script>
    add_css('lib/plugins/jstree/style.css');
    add_js(['lib/plugins/jstree/jstree.min', parse_view_name('Language', 'List')]);
</script>