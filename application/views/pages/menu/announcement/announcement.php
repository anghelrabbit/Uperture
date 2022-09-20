
<section class="content" name="announcement_page">

</section>
<?php $this->load->view('pages/modal/modal_announcement/modal_select_employees') ?>
<script name="announcement_table" type="text/x-custom-template">
<?php $this->load->view('pages/menu/announcement/announcement_template') ?>
</script>
<script name="announcement_compose" type="text/x-custom-template">
<?php $this->load->view('pages/menu/announcement/compose/compose_announcement') ?>
</script>
<script id="hidden-template" type="text/x-custom-template">
    <?php $this->load->view('templates/structure') ?>
</script>

<?php $this->load->view('pages/modal/modal_announcement/modal_announcement') ?>
<script type="text/javascript">
    var extra_data = <?php echo $extra_data ?>;

    document.addEventListener('DOMContentLoaded', function () {
        $(".sidebar-menu").find(".active").removeClass("active");
        $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
        $(".sidebar-menu").find(".menu-open").removeClass("menu-open");
        $('#announcementmenu').addClass('active');
       
    });
</script>
