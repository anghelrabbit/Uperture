<form name="generate_report" action="WorkscheduleReport" method="POST" target="_blank" >
    <input type="text"   name="structure" value="" hidden>
    <input type="text"  name="worksched_in" value="" hidden>
    <input type="text"   name="worksched_out" value="" hidden>
    <input type="text"   name="category" value="" hidden>
    <input type="text"   name="is_pdf" value="" hidden>
    <input type="text"   name="emp_data" value="" hidden>
</form>
<section class="content" >
    <?php $this->load->view('templates/structure') ?>
    <br>
    <div class="box box-primary" id="dashboard_box">
        <div class="box-header with-border">
            <div class="row" >
                <div class="col-md-3">
                    <div class="input-group date " id="" style="color:black">
                        <div class="input-group-addon">
                            <i class="">Pay period from</i>
                        </div>
                        <input type="date" class="form-control pull-right " id="worksched_in" onchange="setupPayPeriod()" onkeyup="$('#datefiled_in').val('')" value="<?php echo date('Y-m-d') ?>">
                    </div>

                </div>
                <div class="col-md-3">
                    <div class="input-group date " id="" style="color:black">
                        <div class="input-group-addon">
                            <i class="">Pay period to</i>
                        </div>
                        <input type="date" class="form-control pull-right " id="worksched_out"  disabled>
                    </div>
                </div>
                <div class="col-lg-2 pull-right">
                    <div class="pull-right">
                        <span class="btn" name="generate_report_modal" style="background-color: #3ED03E;color:white"><b style="letter-spacing: 0.5px" onclick="">Generate Report</b></span>
                    </div>
                </div>

            </div>
        </div>
        <div class="box-body no-padding">
            <div class="row">

                <div class="col-lg-12 ">
                    <div class="pad">
                        <div class="table-responsive">
                            <table id="schedule_summary_table" class="table table-striped table-bordered" style="width:100%; background-color:white">
                                <thead style="background-color:#2692D0;color:white;">
                                    <tr>
                                        <th style="white-space:nowrap;padding-right: 100px" rowspan="2" >Employee</th>
                                        <th style="white-space:nowrap;" rowspan="2">Work Days</th>
                                        <th style="white-space:nowrap;" rowspan="2">Attended</th>
                                        <th style="white-space:nowrap;text-align:center" rowspan="1" colspan="4">Approved Forms</th>
                                    </tr>
                                    <tr>
                                        <th rowspan="1" style="text-align:center">Leave</th>
                                        <th rowspan="1" style="text-align:center">Undertime</th>
                                        <th rowspan="1" style="text-align:center">Change Schedule</th>
                                        <th rowspan="1" style="text-align:center">Overtime</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</section>





<?php $this->load->view('pages/modal/modal_report/modal_report'); ?>           

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        $(".sidebar-menu").find(".active").removeClass("active");
        $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
        $('#employeesmenu').addClass('active');
        $('#emp_workschedule').addClass('active');
        $('#emp_worksched').addClass('text-aqua');
        $('#employeesmenu').addClass('menu-open');
    });
</script>
