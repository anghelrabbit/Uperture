
<form id="generate_transmital_excel" action="BankTransmittal/GenerateExcel" method="POST" target="_blank" >
    <input type="text" name="emps" value="" hidden>
    <input type="text" name="sched_in" value="" hidden>
    <input type="text" name="sched_out" value="" hidden>
    <input type="text" name="category" value="" hidden>
    <input type="text" name="prepared_by" value="" hidden>
    <input type="text" name="checked_by" value="" hidden>
    <input type="text" name="noted_by" value="" hidden>
    <input type="text" name="approved_by" value="" hidden>
    <input type="text" name="file_format" value="" hidden>
</form>
<form id="generate_compensation" action="Compensation/GenerateCompensationReport" method="POST" target="_blank" >
    <input type="text" name="compensate_emps" value="" hidden>
    <input type="text" name="compensate_year" value="" hidden>
</form>
<form id="generate_onemonth_compensation" action="MonthlyRestriction/GenerateOneMonthCompensation" method="POST" target="_blank" >
    <input type="text" name="compensate_onemonth_emps" value="" hidden>
    <input type="text" name="compensate_onemonth_year" value="" hidden>
    <input type="text" name="compensate_onemonth_month" value="" hidden>
</form>
<form id="generate_jobpos_net" action="Compensation/GenerateJobPosNetReport" method="POST" target="_blank" >
    <input type="text" name="jobpos_net_emps" value="" hidden>
     <input type="text" name="worksched_from" value="" hidden>
    <input type="text" name="worksched_to" value="" hidden>
</form>
<form id="generate_payslips" action="Payslip/GenerateEmployeePayslips" method="POST" target="_blank" >
    <input type="text" name="emps_payslip" value="" hidden>
</form>
<span class="btn hidden" name="generate_payroll_excel"></span>
<section class="content">
    <div class="row">
        <div class="col-lg-12">
            <?php $this->load->view('templates/structure'); ?>
            <br>
            <div class="box">
                <div class="box-body">

                    <div class="box-body" style="max-width: 100%;  overflow: auto;">
                        <div class="col-md-3 col-lg-3 col-xs-6" name="div_sched_from">
                            <span class="" style="letter-spacing: 0.5px">From</span>
                            <input type="date" class="form-control" id="worksched_in" onchange="setupPayPeriod()" onkeyup="$('#datefiled_in').val('')" value="<?php echo date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-3 col-lg-3 col-xs-6" name="div_sched_to">
                            <span class="" style="letter-spacing: 0.5px">To</span>
                            <input type="date" class="form-control"   id="worksched_out"  readonly>
                        </div>
                        <div class="col-md-3 col-lg-3 col-xs-6 hidden" name="div_year">
                            <span class="" style="letter-spacing: 0.5px">Year</span>
                            <input type="text" class="form-control "   id="worksched_year">
                        </div>
                        <div class="col-md-3 col-lg-3 col-xs-6">
                            <span class="" style="letter-spacing: 0.5px">Category</span>
                            <select type="date" class="form-control"   name="payroll_category"  >
                                <option value="0">Payroll Summary</option>
                                <option value="1">Bank Transmittal</option>
                                <option value="3">Payslips</option>
                                <option value="4">Compensation</option>
                                <option value="5">Job Position Net Pay</option>
                            </select>
                        </div>
						<div class="col-md-3 col-lg-3 col-xs-6 " name="">
                               <span class="" style="letter-spacing: 0.5px;color:white">.</span><br>
                            <span class="btn  hidden" name="btn_month_restrict" style="letter-spacing: 0.5px;background-color:#3ED03E;color:white">Employee Month Exclusions</span>
                        </div>
                        <div class="pull-right"><span class="btn" name="generate_excel" style="background-color:#3ED03E;color:white;letter-spacing:0.5px">Generate Report</span></div>
                        <table name="employees_table" class="table  table-bordered table-striped" style="width:100%;">
                            <thead style="background-color:#2692D0;color:white;">
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th> <input type="checkbox" name="check_all" onclick="checkReferences()" style="width:20px;height:20px;"></th>
                                    <th style="white-space:nowrap;">Employee
                                        <br>
                                        <input name="employees_lastname" class="form-control" type="text" placeholder="Lastname" style="color:black"/>
                                        <input name="employees_firstname" class="form-control" type="text"  placeholder="Firstname" style="color:black"/>
                                    </th>
                                    <th><center>ID Number</center></th>
                            <th><center>BM #</center></th>
                            <th><center>Job Position</center></th>
                            <th>Gender<br>
                                <select class="form-control" name="employee_gender">
                                    <option value="">All</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </th>
                            <!--<th><center>Present Address</center></th>-->
                            <th><center>Contact #</center></th>
                            </tr>

                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $this->load->view('pages/modal/modal_payroll/modal_payroll') ?>
<?php $this->load->view('pages/modal/modal_monthly_restriction/modal_monthly_restriction') ?>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        $(".sidebar-menu").find(".active").removeClass("active");
        $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
        $('#employeesmenu').addClass('active');
        $('#bank_transmittal').addClass('active');
        $('#bank_transmittal_circle').addClass('text-aqua');
        $('#employeesmenu').addClass('menu-open');
    });
</script>
