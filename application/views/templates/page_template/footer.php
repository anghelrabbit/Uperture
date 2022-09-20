

<?php
$this->load->view('utilities/modal_loading');
if ($this->session->userdata('idleacc') == 1) {
    $this->load->view('utilities/modal_idle');
}
//$this->load->view('pages/modal/settings_modal/setting_modal');
?>
<?php $this->load->view('pages/modal/modal_overtime/modal_overtime_notif'); ?>          
<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; 2022<a href="#">&nbsp;Silver Summit Consulting</a>.</strong> All rights
    reserved.
</footer>

</div>
</body>
<script>
    var isidle = '<?php echo $this->session->userdata('idleacc') ?>';
</script>
<?php foreach ($js as $data) { ?>
    <script src="<?= $data ?>"></script>
<?php } ?>

<script>
    $(function () {

//        $('input').on('keyup', function () {
//
//            var newValue = $(this).val().replace(/'/g, '').replace(/"/g, '');
//            $(this).val(newValue);
//        });
    });
</script>
<script src="assets/myjs/utilities/notification_alert.js" type="text/javascript"></script>
<script src="assets/myjs/utilities/jquery.idle.min.js"></script>
<!--<script src="assets/myjs/utilities/idleko.min.js"></script>-->
<!--<script src="assets/vendors/fontawesome-free-5.14.0-web/js/all.min.js" type="text/javascript"></script>-->
<script src="assets/myjs/utilities/current_dtr.js"></script>
<script src="assets/myjs/utilities/idle_account.js"></script>
<script src="assets/vendors/dist/js/demo.js"></script>

</html>
