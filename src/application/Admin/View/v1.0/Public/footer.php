        </div> <!-- container -->
    </div> <!-- content -->
</div>
<!-- ============================================================== -->
<!-- End Right content here -->
<!-- ============================================================== -->

<!-- Right Sidebar -->
<?php echo fetch_view('right-bar', 'Public');?>
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

load_js('jquery.min');

echo fetch_view('app-js', 'Public');
// <!-- jQuery  -->
//load_js('jquery.min');
//load_js('bootstrap.min');
//load_js('detect');
//load_js('fastclick');
//load_js('jquery.blockUI');
//load_js('waves');
//load_js('jquery.nicescroll');
//load_js('jquery.slimscroll');
//load_js('jquery.scrollTo.min');

// <!-- KNOB JS -->
//

?>

<!-- KNOB JS -->
<!--[if IE]>
<?php //load_js('excanvas', 'plugins/jquery-knob');?>
<![endif]-->

<?php
//load_js('jquery.knob', 'plugins/jquery-knob');

// <!--Morris Chart-->
//load_js('morris.min', 'plugins/morris');
//load_js('raphael-min', 'plugins/raphael');

// <!-- Dashboard init -->
//load_js('jquery.dashboard', 'pages');

// <!-- App js -->
//load_js('jquery.core');
//load_js('jquery.app');
?>

</body>
</html>