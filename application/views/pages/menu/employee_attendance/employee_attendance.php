
<section class="content" >
    <?php // $this->load->view('templates/structure') ?>
    <br>

    <div class="box box-primary" name="dashboard_box">

        <div class="box-header with-border">
            <div class="row">



                <div class="col-md-3 col-lg-3 col-xs-6">
                    <div class="form-group " id="" style="color:black">
                        <span class="" style="letter-spacing: 0.5px">From</span>
                        <input type="date" class="form-control" id="worksched_in" onchange="setupPayPeriod()" onkeyup="$('#datefiled_in').val('')" value="<?php echo date('Y-m-d') ?>">
                    </div>

                </div>
                <div class="col-md-3 col-lg-3 col-xs-6">
                    <div class="form-group " id="" style="color:black">
                        <span class="" style="letter-spacing: 0.5px">To</span>
                        <input type="date" class="form-control" id="worksched_in" onchange="setupPayPeriod()" onkeyup="$('#datefiled_in').val('')" value="<?php echo date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 col-xs-6">
                    <div class="form-group " id="" style="color:black">
                        <span class="" style="letter-spacing: 0.5px">Department</span>
                        <select class="form-control form-control-sm" id="request_division" name="" onchange="tabCategory('');">

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

                    </div>
                </div>
<!--                <div class="col-md-3 col-lg-3 col-xs-6">
                    <div class="form-group " id="" style="color:black">
                        <span class="" style="letter-spacing: 0.5px">Integrations</span>
                        <select class="form-control form-control-sm" id="request_division" name="" onchange="tabCategory('');">

                            <option value="All">All</option>
                            <option value="Paypal">Paypal</option>
                            <option value="Bank">Bank</option>
                            
                        </select>    

                    </div>
                </div>-->

            </div>





        </div>
        <div class="box-body no-padding">
            <div class="row">

                <div class="col-lg-12 col-xs-12 col-md-12">
                    <div class="pad">
                        <div class="nav-tabs-custom " id="table_tabs">
                            <ul class="nav nav-tabs ">
                                <li >

                                </li>
                                <li class="active" >
                                    <a href="#by_cut_off" data-toggle="tab"  onclick="tabCategory(0)">By Cut Off&nbsp;&nbsp;</a>
                                </li>
                                <li class="" >
                                    <a href="#attendance_record" data-toggle="tab"  onclick="tabCategory(0)">Attendance Record&nbsp;&nbsp;</a>
                                </li>


                            </ul>   
                            <div class="tab-content">
                                <div class="active tab-pane" id="by_cut_off" >
                                    <?php $this->load->view('pages/menu/employee_attendance/tabs/by_cut_off'); ?>
                                </div>
                                <div class="tab-pane" id="attendance_record" >
                                    <?php $this->load->view('pages/menu/employee_attendance/tabs/attendance_record'); ?>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>


<?php $this->load->view('pages/modal/modal_leave/modal_leave'); ?>
<?php $this->load->view('pages/modal/modal_undertime/modal_undertime'); ?>
<?php $this->load->view('pages/modal/modal_cs/modal_cs'); ?>
<?php $this->load->view('pages/modal/modal_overtime/modal_overtime'); ?>

<script id="signatory-template" type="text/x-custom-template">
    <?php $this->load->view('templates/signatories') ?>
</script>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        $(".sidebar-menu").find(".active").removeClass("active");
        $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
        $(".sidebar-menu").find(".menu-open").removeClass("menu-open");
        $('#employeeAttendance_menu').addClass('active');

    });
</script>
