
<style>
    .table_border_color>tbody>tr>td {
        border: 1px solid gray;
    }
</style>
<div class="modal modal-primary fade" name="modal_dtr_emp" tabindex="-1"  role="dialog"  aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color:black;font-weight: bold">X</span></button>
                <div >
                </div>
            </div>
            <div class="modal-body">
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
                                            <div  style="max-width: 100%;  overflow: auto;">
                                                <table id="emp_dtr_table" class="table table-bordered" style="width:100%;">
                                                    <thead style="background-color:#3CAE85;color:white;">

                                                        <tr>
                                                            <th rowspan="2" style="white-space:nowrap;"></th>
                                                            <th  colspan="2" style="text-align:center">Schedule IN</th>
                                                            <th  colspan="1" rowspan="2" style="text-align:center">Actual Punch IN</th>
                                                            <th  colspan="2"  style="text-align:center">Schedule OUT</th>
                                                            <th  colspan="1" rowspan="2" style="text-align:center">Actual Punch OUT</th>
                                                        </tr>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Time</th>
                                                            <th>Date</th>
                                                            <th>Time</th>
                                                            <th colspan="1">Data 7</th>
                                                            <th colspan="1">Data 8</th>
                                                            <th colspan="1"></th>
                                                            <th colspan="1"></th>
                                                            <th colspan="1"></th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>