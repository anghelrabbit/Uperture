<style>
    .schedule {
        margin-top:15%;
        text-align: center;
        justify-content: center;
    }
    .fc-day-number{
        color:yellow;
        font-weight: bold;
        font-size: 0.9vw;
        margin-right:4px;
    }

    .calendar-container {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .calendar-text {
    }

    .date-changer {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    @media screen and (max-width: 514px) {
        div .font-change {
            font-size: 10px;
        }
        span .asdasd{
            content:'brebebebeb'
        }
    }
    .font-change {
        font-size:20px;
    }
   


</style>
<section class="content">
    <div class="row ">
        <div class="col-lg-12">  
            <fieldset style="border: 3px solid #2692D0">
                <legend style=" border-style: none;margin:auto;
                        background-color: #2692D0;
                        color:white;
                        border-width: 0;
                        font-size: 15px;
                        line-height: 20px;
                        margin-bottom: 0;
                        width: auto;
                        padding: 0 10px;
                        border: 1px solid #2692D0;">Color Legend</legend>
                <div class="col-lg-12" >
                    <div class="col-lg-1 col-xs-3 font-change" style="height:35px;font-weight:bold;text-align: center;margin:0.5%;background-color: #749AC5;color:white;"><span style="text-align: center;vertical-align: middle;line-height: 34px;">Day Off</span></div>
                    <div class="col-lg-2 col-xs-5 font-change" style="height:35px;font-weight:bold;text-align: center;margin:0.5%;background-color: #3EB3A3;color:white;"><span style="text-align: center;vertical-align: middle;line-height: 34px;">On time</span></div>
                    <div class="col-lg-2 col-xs-3 font-change" style="height:35px;font-weight:bold;text-align: center;margin:0.5%;background-color: #6A55AE;color:white;"><span style="text-align: center;vertical-align: middle;line-height: 34px;">Leave</span></div>
                    <div class="col-lg-2 col-xs-3 font-change" style="height:35px;font-weight:bold;text-align: center;margin:0.5%;background-color: #FFB347;color:white;"><span style="text-align: center;vertical-align: middle;line-height: 34px;">Missing </span></div>
                    <div class="col-lg-2 col-xs-5 font-change" style="height:35px;font-weight:bold;text-align: center;margin:0.5%;background-color: #FF392E;color:white;"><span style="text-align: center;vertical-align: middle;line-height: 34px;">Late In / Early Out</span></div>
                    <div class="col-lg-2 col-xs-3 font-change" style="height:35px;font-weight:bold;text-align: center;margin:0.5%;background-color: #ECA1AC;color:white;"><span style="text-align: center;vertical-align: middle;line-height: 34px;">Holiday</span></div>
                </div>
            </fieldset>
        </div>
        <div class="col-md-12">
            <div class="box box-solid" style="background-color:#CBD7E3">
<!--                <div class="box-header with-border ">
                    <ul class="nav nav-tabs">
                        <li   class="active" onclick="" ><a   href="#daily_view" data-toggle="tab" style="font-weight: bold;">Pay Period View</a></li>
                        <li   onclick="" ><a name="payslip_tab" href="#calendar_view" data-toggle="tab" style="font-weight: bold;"> Monthly View  </a> </li>
                        <li class="box-tools pull-right">
                        </li>
                    </ul>   

                </div>-->
                <div class="box-body ">
                    <div class="nav-tabs-custom">
                        <div class="tab-content" style="background-color:#CBD7E3">
                            <div class="active tab-pane" id="daily_view">
                                <div class="row" style="  align-items: center;">
                                    <div class="date-changer" name="worksched_inout">
                                        <div class="col-md-3 col-xs-12">
                                            <div class="form-group " id="" style="color:black">
                                                <span class="" style="letter-spacing: 0.5px">Schedule From</span>
                                                <input type="date" class="form-control" id="worksched_in" onchange="setupPayPeriod()" onkeyup="$('#datefiled_in').val('')" value="<?php echo date('Y-m-d') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-12">
                                            <div class="form-group" id="" style="color:black">
                                                <span class="" style="letter-spacing: 0.5px">Schedule To</span>
                                                <input type="date" class="form-control"   id="worksched_out"  readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div  style="max-width: 100%;  overflow: auto;">
                                    <table id="my_dtr_table" class="table table-striped table-bordered" style="width:100%;">
                                        <thead style="background-color:#2692D0;color:white;">
                                            <tr>
                                                <th rowspan="2"></th>
                                                <th colspan="3" style="text-align:center">Time-in</th>
                                                <th colspan="3" style="text-align:center">Time-out</th>
                                            </tr>
                                            <tr>
                                                <th  colspan="1" style="text-align:center">Work Schedule IN</th>
                                                <th  colspan="1" style="text-align:center"></th>
                                                <th  colspan="1" style="text-align:center">Actual Punch IN</th>
                                                <th  colspan="1" style="text-align:center">Work Schedule OUT</th>
                                                <th  colspan="1" style="text-align:center"></th>
                                                <th  colspan="1" style="text-align:center">Actual Punch OUT</th>
                                                <th  colspan="1" style="text-align:center"></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="calendar_view">
                                <div name="my_calendar"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





</section>


<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        $(".sidebar-menu").find(".active").removeClass("active");
        $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
        $('#myaccountmenu').addClass('active');
        $('#DTRHolder').addClass('active');
        $('#dtrcircle').addClass('text-aqua');
        $('#myaccountmenu').addClass('menu-open');
    });
</script>