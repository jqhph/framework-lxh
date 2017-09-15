        </div> <!-- container -->
    </div> <!-- content -->
</div>
<!-- ============================================================== -->
<!-- End Right content here -->
<!-- ============================================================== -->

<!-- Right Sidebar -->
<?php echo render_view('public.right-bar');?>
<!-- /Right-bar -->

</div>
<!-- END wrapper -->

<footer class="footer text-right">
    2016 © Adminto.
</footer>

<script>
    var resizefunc = [];
</script>

<?php

echo load_js('jquery.min');

echo render_view('public.app-js');
// <!-- jQuery  -->
//echo load_js('jquery.min');
//echo load_js('bootstrap.min');
//echo load_js('detect');
//echo load_js('fastclick');
//echo load_js('jquery.blockUI');
//echo load_js('waves');
//echo load_js('jquery.nicescroll');
//echo load_js('jquery.slimscroll');
//echo load_js('jquery.scrollTo.min');

// <!-- KNOB JS -->
//

?>

<!-- KNOB JS -->
<!--[if IE]>
<?php //echo load_js('excanvas', 'plugins/jquery-knob');?>
<![endif]-->

<?php
//echo load_js('jquery.knob', 'plugins/jquery-knob');

// <!--Morris Chart-->
//echo load_js('morris.min', 'plugins/morris');
//echo load_js('raphael-min', 'plugins/raphael');

// <!-- Dashboard init -->
//echo load_js('jquery.dashboard', 'pages');

// <!-- App js -->
//echo load_js('jquery.core');
//echo load_js('jquery.app');
?>

</body>
</html>