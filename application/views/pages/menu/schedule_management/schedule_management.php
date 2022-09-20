    
<!-- Content Header (Page header) -->


<section class="content">
    <div class="row">
        <div class="col-lg-12">
<!--            <div name="page_struct_holder">
            </div>-->
            <br>
            <div class="box">
                <div class="box-body">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs " name="" > 
                            <li class="active" name="" ><a  href="#schedule_management" data-toggle="tab">Schedule Management<i  class=""></i></a></li>
                            <li class=""  name=""><a name="monthly_view_tab" href="#monthly_view" data-toggle="tab">Monthly View</i></a></li>
                        </ul>
                        <div class="tab-content" style="color:black">
                            <div class="active tab-pane" id="schedule_management">
                                <div class="row">
                                    <div class="col-md-3 col-lg-3 col-xs-6">
                                        <div class="form-group " id="" style="color:black">
                                            <span class="" style="letter-spacing: 0.5px">Schedule From</span>
                                            <input type="date" class="form-control" id="worksched_in" onchange="setupPayPeriod()"  value="<?php echo date('Y-m-d') ?>">
                                        </div>

                                    </div>
                                    <div class="col-md-3 col-lg-3 col-xs-6">
                                        <div class="form-group" id="" style="color:black">
                                            <span class="" style="letter-spacing: 0.5px">Schedule To</span>
                                            <input type="date" class="form-control" readonly   id="worksched_out"  >
                                        </div>
                                    </div>
                                </div>
                                <?php $this->load->view('pages/menu/schedule_management/tabs/sched_manage') ?>
                            </div>
                            <div class="tab-pane" id="monthly_view">
                                <?php $this->load->view('pages/menu/schedule_management/tabs/monthly_view') ?>
                            </div>

                        </div>

                    </div>
                </div>


            </div>
        </div>
    </div>
</div>


</section>
<?php $this->load->view('pages/modal/modal_scheduler/modal_scheduler') ?>
<?php $this->load->view('pages/modal/modal_scheduler/modal_setup_schedule') ?>
<?php $this->load->view('pages/modal/modal_schedule/modal_schedule') ?>
<?php $this->load->view('pages/modal/modal_dtr_emp/modal_dtr_emp') ?>
<script id="hidden-template" type="text/x-custom-template">
    <?php $this->load->view('templates/structure') ?>
</script>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        $(".sidebar-menu").find(".active").removeClass("active");
        $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
        $('#payrolladjustments').addClass('active');
        $('#payrolladjustments').addClass('menu-open');
        $('#schedulemanagementmenu').addClass('active');
        $('#schedulemanagementcircle').addClass('text-aqua');

    });
</script>
