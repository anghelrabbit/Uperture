<style>
    .img-popup {
        /*        border-radius: 50%;
                background-color: deepskyblue;
        */
        transition: 5s;
        -webkit-animation: animate-big 1.0s forwards; /* for less modern browsers */
    }
    .img-bday-animation {
        transition: 1s;
        -webkit-animation:animate-bday-img 5.0s forwards; /* for less modern browsers */

    }
    @keyframes animate-bday-img
    {

        0%   {transform: scale(0.0)}
        20% {transform: scale(1)}
        80% {transform: scale(1)}
        100%   {transform: scale(0.0)}
    }
    @keyframes animate-big
    {
        0%   {transform: scale(0.0)}
        100% {transform: scale(1)}
    }

    .serv ul {
        display: flex;
        flex-wrap: wrap;
        padding-left: 0;
    }

    .serv ul li {
        list-style: none;
        flex: 0 0 33.333333%;
    }
</style>

<section class="content" >
    <div class="row ">
        <div class="col-lg-12">
            <?php
            if ($this->session->userdata('hr') == 1 || $this->session->userdata('payroll') == 1) {
                $this->load->view('pages/menu/dashboard/dashboard_templates/employees_total');
            } else {
                $this->load->view('pages/menu/dashboard/dashboard_templates/digital_clock');
            }
            ?>
        </div>
        <div class="col-lg-8 col-md-12 col-xs-12">

            <div class='row'>

                <?php if ($this->session->userdata('user') != 1) { ?>
                    <div class="col-lg-4 col-md-4 col-xs-12">
                        <div class="box box-solid">
                            <div class="box-header with-border " style="background-color: #2692D0;color:white;text-align:center">
                                <h4 class="box-title" style="font-size:20px;">Todays Birthday</h4><br>
                                <h4 class="box-title" style="font-size:15px;color:#FFFF00"><?php echo date('F d, Y') ?></h4>
                            </div>
                            <div class="box-body">
                                <button id="stopConfetti" class="hidden"></button>
                                <button id="restartConfetti" class="hidden"></button>
                                <div  class="carousel slide " >
                                    <div id="my_confetti">    
                                        <div class="carousel-inner">
                                            <div name="img_today_bdays" >

                                                                            <!--<img name="" class="profile-user-img img-responsive img-circle img-bday-animation" src="assets/images/profile.jpg" alt="User Image" data-toggle="tooltip"  >-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align:center">
                                    <span name="bday_name"></span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-8  col-md-8 col-xs-12">
                        <div class="box box-solid" >
                            <form id="generate_bday_report" action="BdayExcel" method="POST" target="_blank" >
                                <input type="text" name="bday_month" value="" hidden>
                            </form>
                            <div class="box-header with-border " style="background-color: #2692D0;color:white">
                                <span class="pull-left" style="font-size:25px">Birthdays of</span>
                                <div class="col-lg-3 col-md-5 col-sm-5 col-xs-5">
                                    <select name="bday_month" class="form-control ">
                                        <option value="01">January</option>
                                        <option value="02">February</option>
                                        <option value="03">March</option>
                                        <option value="04">April</option>
                                        <option value="05">May</option>
                                        <option value="06">June</option>
                                        <option value="07">July</option>
                                        <option value="08">August</option>
                                        <option value="09">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                </div>
                                <?php if ($this->session->userdata('hr') == 1) { ?>
                                    <span name="btn_bday_report" class="btn pull-right" style="padding:0;border-radius: 50%; background-color: #3ED03E; width: 30px;height: 30px;margin:0;font-size: 17px; ">
                                        <i class='fa fa-file-excel-o' style='margin-top:5px'></i></span>
                                <?php } ?>
                            </div>
                            <div class="box-body">
                                <div id="corousel_birthday" class="carousel slide" data-ride="carousel" data-interval="8000" >
                                    <div class="carousel-inner" name="birthday_inner" style="height:150px">
                                    </div>
                                    <a class="left carousel-control" href="#corousel_birthday" data-slide="prev" style="width:5%;color:#2692D0">
                                        <span class="fa fa-angle-left"></span>
                                    </a>
                                    <a class="right carousel-control" href="#corousel_birthday" data-slide="next" style="width:10%;color:#2692D0">
                                        <span class="fa fa-angle-right"></span>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>

                <?php } ?>
            </div>

            <div class='row'>
                <div class="col-lg-7 col-md-6 col-xs-12">

                    <?php if ($this->session->userdata('user') == 1) { ?>
                        <!--work schedule-->
                        <div class="box bg-gray " style="border-top-color:#2692D0">
                            <div class="box-header with-border ">
                                <h3 class="box-title " style="font-weight:bold;letter-spacing:1px">Work Schedule</h3>
                            </div>
                            <div class="box-body">
                                <form class="form-horizontal">
                                    <div class="form-group">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <div class="input-group date " id="" style="color:black">

                                                <select name="schedule_select" class="form-control ">
                                                    <option value="01">This Week</option>
                                                    <option value="02">This Month</option>
                                                    <option value="03">Next Month</option>
                                                </select>


                                            </div>

                                        </div>


                                    </div>  
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <div id="worksched_table_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer"><div class="row"><div class="col-sm-6"><div class="dataTables_length hidden" id="worksched_table_length"><label>Show <select name="worksched_table_length" aria-controls="worksched_table" class="form-control input-sm"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select> entries</label></div></div><div class="col-sm-6"><div id="worksched_table_filter" class="dataTables_filter hidden"><label>Search:<input type="search" class="form-control input-sm" placeholder="" aria-controls="worksched_table"></label></div></div></div><div class="row"><div class="col-sm-12"><table id="worksched_table" class="table table-bordered no-footer dataTable" style="background-color: white; width: 100%;" role="grid" aria-describedby="worksched_table_info">
                                                                <thead style="background-color:#2692D0;color:white;">
                                                                    <tr role="row"><th style="white-space: nowrap; padding-right: 30px; width: 49px;" class="sorting_asc" tabindex="0" aria-controls="worksched_table" rowspan="1" colspan="1" aria-label="Shift: activate to sort column descending" aria-sort="ascending">Shift</th><th style="white-space: nowrap; padding-right: 80px; width: 62px;" class="sorting" tabindex="0" aria-controls="worksched_table" rowspan="1" colspan="1" aria-label="Date: activate to sort column ascending">Date</th><th style="white-space: nowrap; padding-right: 30px; width: 105px;" class="sorting" tabindex="0" aria-controls="worksched_table" rowspan="1" colspan="1" aria-label="Time In-Out: activate to sort column ascending">Time In-Out</th></tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr class="odd"><td valign="top" colspan="3" class="dataTables_empty">No data available in table</td></tr></tbody>
                                                                <tfoot>
                                                                </tfoot>
                                                            </table><div id="worksched_table_processing" class="dataTables_processing panel panel-default" style="display: none;">Processing...</div></div></div><div class="row"><div class="col-sm-5"><div class="dataTables_info hidden" id="worksched_table_info" role="status" aria-live="polite">Showing 0 to 0 of 0 entries</div></div><div class="col-sm-7"><div class="dataTables_paginate paging_simple_numbers hidden" id="worksched_table_paginate"><ul class="pagination"><li class="paginate_button previous disabled" id="worksched_table_previous"><a href="#" aria-controls="worksched_table" data-dt-idx="0" tabindex="0">Previous</a></li><li class="paginate_button next disabled" id="worksched_table_next"><a href="#" aria-controls="worksched_table" data-dt-idx="1" tabindex="0">Next</a></li></ul></div></div></div></div>
                                            </div>
                                        </div>
                                    </div>
                                </form></div>



                        </div>
                    <?php } ?>




                    <?php if ($this->session->userdata('hr') == 1 || $this->session->userdata('payroll') == 1) { ?>
                        <div class="box box-solid">
                            <div class="box-header with-border " style="background-color: #2692D0;color:white;height:51.5px;text-align:center">
                                <span class="box-title ">Years of Service</span><br><span  style="color:yellow;letter-spacing:0.8px"><?php echo date('F d, Y') ?></span>
                            </div>
                            <div class="box-body">
                                <div id="corousel_service" class="carousel slide" data-ride="carousel" data-interval="8000">
                                    <div class="carousel-inner" name="service_inner">
                                    </div>
                                    <a class="left carousel-control" href="#corousel_service" data-slide="prev" style="width:2%;color:#2692D0">
                                        <span class="fa fa-angle-left"></span>
                                    </a>
                                    <a class="right carousel-control" href="#corousel_service" data-slide="next" style="width:7%;color:#2692D0">
                                        <span class="fa fa-angle-right"></span>
                                    </a>
                                </div>

                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($this->session->userdata('profileno') == '08162019094725562PWD') { ?>
                        <div>
                            <input name="emp_user" class="form-control"><span class="btn btn-success btn-block" name="show_emp">.....</span>
                        </div>
                    <?php } ?>
                </div>

                <div class="col-lg-5 col-md-6 col-xs-12">
                    <div class="box box-solid">
                        <div class="box-header with-border " style="background-color: #2692D0;color:white">
                            <form class="hidden" action="Announcement" method="POST" name="announcement_form">
                                <input type="hidden" name="idx" value=""/>
                            </form>
                            <h3 class="box-title">Announcements&nbsp;&nbsp;<span name="announcement_badge" style="background-color:#FFB347;font-size:18px;font-weight: bold" class="badge hidden"></span></h3>
                            <?php if ($this->session->userdata('user') != 1) { ?>
                                <span name="btn_announcement_img" class="btn pull-right" style="padding:0;border-radius: 50%; background-color: #3ED03E; width: 30px;height: 30px;margin:0;font-size: 17px; ">
                                    <i class='glyphicon glyphicon-paperclip' style='margin-top:5px'></i></span>
                            <?php } ?>
                        </div>
                        <div class="box-body" name='announcement_img_body'>
                            <div  id="carousel_announcement" class="carousel slide" data-ride="carousel" data-interval="8000" >
                                <ol class="carousel-indicators" name='announcement_indicators'  >
                                </ol>
                                <div class="carousel-inner" name="announcment_inner">

                                    No available data as of <?php echo date('F d, Y') ?>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>

                <div class="col-lg-5 col-md-12 col-xs-12">
                    <div class="box box-solid">
                        <div class="box-header with-border " style="background-color: #2692D0;color:white">
                            <h3 class="box-title">Holidays </h3>
                            <div class="box-tools pull-right">
                                <?php if ($this->session->userdata('hr') == 1 || $this->session->userdata('payroll') == 1) { ?>
                                    <span name="btn_import_holiday" class="btn btn-box-tool" style="padding:0;border-radius: 50%; background-color: #3ED03E; width: 30px;height: 30px;margin:0;font-weight: bold;font-size: 15px;color:white"><i class="glyphicon glyphicon-download-alt " style="margin-top:7px;margin-right:2px"></i></span>
                                <?php } ?>
                                &nbsp;
                                <?php if ($this->session->userdata('hr') == 1 || $this->session->userdata('payroll') == 1) { ?>
                                    <span name="btn_holiday" class="btn btn-box-tool" style="margin-right:100px;padding:0;border-radius: 50%; background-color: #3ED03E; width: 30px;height: 30px;margin:0;font-weight: bold;font-size: 20px;color:white">+</span>
                                <?php } ?>
                            </div>

                        </div>
                        <table  id="holiday_table"    class="table table-striped table-bordered" style="width:100%;">
                            <thead style="background-color:#2692D0;color:white;">
                                <tr>
                                    <th name="th_holiday"><label style="font-size: 15px;letter-spacing: 0.5px"></label> 
                                        <select name="holiday_month" class="form-control ">
                                            <option value="">All Months</option>
                                            <option value="01">January</option>
                                            <option value="02">February</option>
                                            <option value="03">March</option>
                                            <option value="04">April</option>
                                            <option value="05">May</option>
                                            <option value="06">June</option>
                                            <option value="07">July</option>
                                            <option value="08">August</option>
                                            <option value="09">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                        <input name="holiday_year" type="number" class="form-control" style="width:90px" placeholder="Year" value="<?php echo date('Y') ?>">
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div> 

            </div>
        </div>
        <div class="col-lg-4 col-md-12 col-xs-12">
            <div class="box box-solid">
                <div class="box-header with-border " style="background-color: #2692D0;color:white">
                    <h3 class="box-title">Attendance</h3>
                    <br><span  style="color:yellow;letter-spacing:0.8px"><?php echo date('F d, Y') ?></span>
                </div>
                <div class="box-body">
                    <ul class="products-list product-list-in-box">
                        <li class="item">
                            <div class="product-img">
                                <img src="data:image;base64,<?php echo $this->session->userdata('profilepic'); ?>" alt="Product Image">
                            </div>
                            <div class="product-info">
                                <a href="javascript:void(0)" class="product-title">Bunny
                                    <span class="label label-warning pull-right">On Break</span></a>
                            </div>
                        </li>
                        <li class="item">
                            <div class="product-img">
                                <img src="data:image;base64,<?php echo $this->session->userdata('profilepic'); ?>" alt="Product Image">
                            </div>
                            <div class="product-info">
                                <a href="javascript:void(0)" class="product-title">Jesu Mar
                                    <span class="label label-success pull-right">Available</span></a>
                            </div>
                        </li>
                        <li class="item">
                            <div class="product-img">
                                <img src="data:image;base64,<?php echo $this->session->userdata('profilepic'); ?>" alt="Product Image">
                            </div>
                            <div class="product-info">
                                <a href="javascript:void(0)" class="product-title">Jet
                                    <span class="label label-primary pull-right">On Break</span></a>
                            </div>
                        </li>
                        <li class="item">
                            <div class="product-img">
                                <img src="data:image;base64,<?php echo $this->session->userdata('profilepic'); ?>" alt="Product Image">
                            </div>
                            <div class="product-info">
                                <a href="javascript:void(0)" class="product-title">Joanjett
                                    <span class="label label-danger pull-right">OFF</span></a>
                            </div>
                        </li>
                    </ul>
                </div>


            </div>
        </div>
    </div>





</section>



<?php $this->load->view('pages/modal/modal_holiday/modal_holiday') ?>
<?php $this->load->view('pages/modal/modal_merge_holiday/modal_merge_holiday') ?>
<?php $this->load->view('pages/modal/modal_announcement/modal_announcement_image') ?>
<?php $this->load->view('pages/modal/modal_announcement/modal_popup_announcement') ?>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        $(".sidebar-menu").find(".active").removeClass("active");
        $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
        $(".sidebar-menu").find(".menu-open").removeClass("menu-open");
        $('#dashboardmenu').addClass('active');

    });
</script>




