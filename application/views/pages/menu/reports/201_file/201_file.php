    
<!-- Content Header (Page header) -->

<form id="generate_201_report" action="Emp201File/Generate201PDF" method="POST" target="_blank" >
    <input type="text" name="structure_pdf" value="" hidden>
    <input type="text" name="lastname_pdf" value="" hidden>
    <input type="text" name="firstname_pdf" value="" hidden>
    <input type="text" name="sex_pdf" value="" hidden>
    <input type="text" name="years_service_pdf" value="" hidden>
</form>
<form id="generate_201_excel" action="Emp201File/Employee201ExcelReport" method="POST" target="_blank" >
    <input type="text" name="structure" value="" hidden>
    <input type="text" name="lastname" value="" hidden>
    <input type="text" name="firstname" value="" hidden>
    <input type="text" name="sex" value="" hidden>
    <input type="text" name="years_service" value="" hidden>
</form>
<section class="content">
    <form class="hidden" action="Profile" method="POST" name="profile_form">
        <input type="hidden" name="profileno" value=""/>
    </form>
    <div class="row">
        <div class="col-lg-12">
            <?php $this->load->view('pages/menu/dashboard/dashboard_templates/employees_total'); ?>
            <?php // $this->load->view('templates/structure'); ?>
            <br>
            <div class="box">
                <div class="box-body">

                    <div class="box-body" style="max-width: 100%;  overflow: auto;">

                        <table id="employee_201_table" class="table  table-bordered table-striped" style="width:100%;">
                            <thead style="background-color:#2692D0;color:white;">
                                <tr>
                                    <th></th>
                                   <th style="padding-right: 70px"><center>Action</center></th>

                            <th style="white-space:nowrap;">Employee
                                <br>
                                <input name="201_lastname" class="form-control" type="text" placeholder="Lastname" style="color:black"/>
                                <input name="201_firstname" class="form-control" type="text"  placeholder="Firstname" style="color:black"/>
                            </th>
                            <th><center>Username</center></th>
                            <th style="white-space:nowrap;">
                                Years of Service
                                <br>
                                <input  type="number" class="form-control" placeholder="Years" style="color:black;width:100px" name="201_years" />
                                <input  type="number" class="form-control" placeholder="Months" style="color:black;width:90px" name="201_months"/>
                            </th>

                            <th>
                                Department<br>
                                <select class="form-control" name="201_department">
                                    <option value="All">All</option>
                                    <option value="DIV-003">Business Development</option>
                                    <option value="DIV-004">Home Ambassadors</option>
                                    <option value="DIV-005">Book That Condo</option>
                                    <option value="DIV-006">Royalty Cleaning</option>
                                    <option value="DIV-007">Customer Service</option>
                                    <option value="DIV-009">Night Calls</option>
                                    <option value="DIV-010">Owner Experience</option>
                                    <option value="DIV-011">Accounting</option>
                                    <option value="DIV-008">Tahoe Truckee</option>
                                    <option value="DIV-012">Training Development</option>
                                </select>
                            </th>
                            <th><center>Position</center></th>
                            <th><center>Job Status</center></th>
                            <th><center>Date Hired</center></th>
                            <th><center>Referral Person</center></th>
                            <th><center>PTO Credentials</center></th>
                            <th><center>Sick Leave Credentials</center></th>
                            <th><center>MBTI Result</center></th>
                            <th><center>DISC Result</center></th>
                            <th><center>Resume</center></th>

                            </tr>

                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php $this->load->view('pages/modal/modal_account/modal_account'); ?>       
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        $(".sidebar-menu").find(".active").removeClass("active");
        $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
        $('#employeesmenu').addClass('active');
        $('#employee201').addClass('active');
        $('#employee201_circle').addClass('text-aqua');
        $('#employeesmenu').addClass('menu-open');
    });
</script>
