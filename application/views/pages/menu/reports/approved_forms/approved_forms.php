<style>
</style>
<section class="content" >
    <?php $this->load->view('templates/structure') ?>
    <br>
    <div class="box box-primary" id="dashboard_box">
        <div class="box-header with-border">
            <div class="row" >
                <div class="col-lg-1 col-md-2 col-sm-2  col-xs-12">
                    <div class="form-group">
                        <span style="letter-spacing:0.5px">By Cut-off</span><br>
                        <input name="cut_off_toggle" checked type="checkbox" onchange="" >
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 col-xs-6">
                    <div class="form-group " id="" style="color:black">
                        <span class="" style="letter-spacing: 0.5px">Approve Forms From</span>
                        <input type="date" class="form-control" id="worksched_in" onchange="setupPayPeriod()" onkeyup="$('#datefiled_in').val('')" value="<?php echo date('Y-m-d') ?>">
                    </div>

                </div>
                <div class="col-md-3 col-lg-3 col-xs-6">
                    <div class="form-group" id="" style="color:black">
                        <span class="" style="letter-spacing: 0.5px">Approve Forms To</span>
                        <input type="date" class="form-control"   id="worksched_out"  readonly>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-xs-12 pull-right">
                    <form name="generate_report" action="GenerateReport" method="POST" target="_blank" >
                        <input type="text"   name="structure" value="" hidden>
                        <input type="text"  name="worksched_in" value="" hidden>
                        <input type="text"   name="worksched_out" value="" hidden>
                        <input type="text"   name="category" value="" hidden>
                    </form>
                    <form name="generate_excel_report" action="GenerateExcelReport" method="POST" target="_blank" >
                        <input type="text"   name="excel_structure" value="" hidden>
                        <input type="text"  name="excel_worksched_in" value="" hidden>
                        <input type="text"   name="excel_worksched_out" value="" hidden>
                        <input type="text"   name="excel_category" value="" hidden>
                    </form>
                    <div class="pull-right">
                        <span class="btn" name="pdf_form" style="background-color: #3ED03E;color:white"><b class="change" style="letter-spacing: 0.5px">PDF File</b></span>
                        <span class="btn" name="excel_form" style="background-color: #3ED03E;color:white"><b class="change" style="letter-spacing: 1px">Excel File</b></span>
                    </div>
                </div>

            </div>
        </div>
        <div class="nav-tabs-custom">
            <div class="tab-content">
                <ul class="nav nav-tabs ">
                    <li class="active" >
                        <a href="#approvedleave" data-toggle="tab"  onclick="tabCategory(0)">Leave&nbsp;&nbsp;</a>
                    </li>
                    <li  class="">
                        <a href="#approvedundertime" data-toggle="tab"  onclick = "tabCategory(1)">Undertime &nbsp;&nbsp;</a>
                    </li>
                    <li >
                        <a href="#approvedchangesched" data-toggle="tab" onclick="tabCategory(2);">Change Schedule&nbsp;&nbsp; </a>
                    </li>
                    <li >
                        <a href="#approvedovertime" data-toggle="tab" onclick="tabCategory(3);">Overtime&nbsp;&nbsp; </a>
                    </li>

                </ul>   
                <div class="active tab-pane" id="approvedleave"  >
                    <?php $this->load->view('pages/menu/reports/approved_forms/tabs/approved_leave'); ?>
                </div>
                <div class=" tab-pane" id="approvedundertime"   >
                    <?php $this->load->view('pages/menu/reports/approved_forms/tabs/approved_undertime'); ?>
                </div>
                <div class="tab-pane" id="approvedchangesched"  >
                    <?php $this->load->view('pages/menu/reports/approved_forms/tabs/approved_cs'); ?>
                </div>
                <div class="tab-pane" id="approvedovertime"  >
                    <?php $this->load->view('pages/menu/reports/approved_forms/tabs/approved_overtime'); ?>
                </div>
            </div>


        </div>
</section>




</div>




<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        $(".sidebar-menu").find(".active").removeClass("active");
        $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
        $('#employeesmenu').addClass('active');
        $('#request_form_reports').addClass('active');
        $('#form_report_circle').addClass('text-aqua');
        $('#employeesmenu').addClass('menu-open');
    });
</script>
