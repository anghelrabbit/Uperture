<style>
    .samplecircle {
        /*        border-radius: 50%;
                background-color: deepskyblue;
        */
        transition: 1s;
        -webkit-animation: samplebig 1.0s forwards; /* for less modern browsers */
    }
    @keyframes samplebig
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
        <div class="col-lg-8 col-md-12 col-xs-12">
            <div class="col-lg-12  col-md-12 col-xs-12">
                <div class="box box-solid">
                    <div class="box-header with-border " style="background-color: #2692D0;color:white">
                        <div class="col-lg-3 col-md-5 col-sm-5 col-xs-5">
                            <input type="date" class="form-control" value="<?php echo date('Y-m-d') ?>" name="employee_birthdate">
                        </div>
                        <h3 class="box-title" style="font-size:30px">Birthdays</h3>
                    </div>
                    <div class="box-body">
                        <button id="stopConfetti" class="hidden"></button>
                        <button id="restartConfetti" class="hidden"></button>
                        <div id="corousel_birthdate" class="carousel slide " data-ride="carousel" data-interval="8000">
                            <ol class="carousel-indicators" name="birthdate_indicators">

                            </ol>
                            <div id="my_confetti" >
                                <div class="carousel-inner" name="birthdate_inner">
                                    <div class="item active">
                                        <ul class="users-list clearfix" name="">
                                            No available data as of <?php echo date('F d, Y') ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            

            <div class="col-lg-6 col-md-12 col-xs-12">

                <div class="box box-solid">
                    <div class="box-header with-border " style="background-color: #2692D0;color:white;height:51.5px">
                        <h3 class="box-title">Years of Service as of (<label style="color:yellow"><?php echo date('F d, Y') ?></label>)</h3>
                    </div>
                    <div class="box-body">
                        <div id="corousel_service" class="carousel slide serv" data-ride="carousel" data-interval="8000">
                            <div class="carousel-inner" name="service_inner">
                            </div>
                            <a class="left carousel-control" href="#corousel_service" data-slide="prev" style="width:5%">
                                <span class="fa fa-angle-left"></span>
                            </a>
                            <a class="right carousel-control" href="#corousel_service" data-slide="next" style="width:10%">
                                <span class="fa fa-angle-right"></span>
                            </a>
                        </div>

                    </div>
                </div>




            </div>

            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="box box-solid">
                    <div class="box-header with-border " style="background-color: #2692D0;color:white">
                        <h3 class="box-title">Announcements</h3>
                        <?php if ($this->session->userdata('hr') == 1) { ?>
                            <span name="btn_announcement_img" class="btn pull-right" style="padding:0;border-radius: 50%; background-color: #3ED03E; width: 30px;height: 30px;margin:0;font-size: 17px; ">
                                <i class='glyphicon glyphicon-paperclip' style='margin-top:5px'></i></span>
                        <?php } ?>
                    </div>
                    <div class="box-body" name='announcement_img_body'>
                        <div id="carousel_announcement" class="carousel slide" data-ride="carousel" data-interval="8000">
                            <ol class="carousel-indicators" name='announcement_indicators'>
                            </ol>
                            <div class="carousel-inner" name="announcment_inner">
                                No available data as of <?php echo date('F d, Y') ?>
                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </div>
        <div class="col-lg-4 col-md-12 col-xs-12">
            <div class="box box-solid">
                <div class="box-header with-border " style="background-color: #2692D0;color:white">
                    <h3 class="box-title">Holidays </h3>
                    <?php if ($this->session->userdata('hr') == 1) { ?>
                        <span name="btn_holiday" class="btn pull-right" style="padding:0;border-radius: 50%; background-color: #3ED03E; width: 30px;height: 30px;margin:0;font-weight: bold;font-size: 20px">+</span>
                    <?php } ?>
                </div>
                <table name="holiday_table" class="table table-striped table-bordered" style="width:100%;">
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





</section>



<?php $this->load->view('pages/modal/modal_holiday/modal_holiday') ?>
<?php $this->load->view('pages/modal/modal_announcement/modal_announcement_image') ?>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        $(".sidebar-menu").find(".active").removeClass("active");
        $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
        $(".sidebar-menu").find(".menu-open").removeClass("menu-open");
        $('#dashboardmenu').addClass('active');

    });
</script>




