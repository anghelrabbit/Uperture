<div class="modal modal-primary fade" name="modal_setup_schedule" tabindex="-1"  role="dialog"  aria-hidden="true" >
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" name="btn_close_modal_setup_schedule">
                    <span aria-hidden="true" style="color:black;font-weight: bold">X</span></button>
            </div>
            <div class="modal-body">
                <div class="tab-content">
                    <form  id="schedule_form">
                        <div class="row">
                            <div class="col-lg-3" >
                                <div class="form-group ">
                                    <label style="color:black" for="">Schedule Type</label>
                                    <select class="form-control" name="sched_type"  >
                                        <option value="0">Schedule 1</option>
                                        <option value="1">Schedule 2</option>
                                        <option value="2">Schedule 3</option>
                                        <option value="3">Schedule 4</option>
                                        <option value="4">Schedule 5</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <div class="form-group ">
                                    <div class="col-lg-12 col-md-12 col-xs-12">
                                        <label style="color:black" for="">Days of Schedule</label>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-xs-4">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" style="width:20px;height:20px;" name="whole_week_box" > 
                                            <span style="font-size: 13px;letter-spacing: 0.5px">Whole Week</span>
                                        </label>
                                    </div>
                                    <div name="dayweek_holder">
                                        <div class="col-lg-1 col-md-1 col-xs-1" >
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" style="width:20px;height:20px;" name="sun_box" >
                                                <span style="font-size: 13px; letter-spacing: 0.5px">Sun</span>
                                            </label>
                                        </div>
                                        <div class="col-lg-1 col-md-1 col-xs-1">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" style="width:20px;height:20px;" name="mon_box" > 
                                                <span style="font-size: 13px;letter-spacing: 0.5px">Mon</span>
                                            </label>
                                        </div>
                                        <div class="col-lg-1 col-md-1 col-xs-1" >
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" style="width:20px;height:20px;" name="tue_box"  > 
                                                <span style="font-size: 13px;letter-spacing: 0.5px">Tue</span>
                                            </label>
                                        </div>
                                        <div class="col-lg-1 col-md-1 col-xs-1" >
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" style="width:20px;height:20px;" name="wed_box"  > 
                                                <span style="font-size: 13px;letter-spacing: 0.5px">Wed</span>
                                            </label>
                                        </div>
                                        <div class="col-lg-1 col-md-1 col-xs-1" >
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" style="width:20px;height:20px;" name="thu_box" >
                                                <span style="font-size: 13px; letter-spacing: 0.5px">Thu</span>
                                            </label>
                                        </div>
                                        <div class="col-lg-1 col-md-1 col-xs-1" >
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" style="width:20px;height:20px;" name="fri_box" >
                                                <span style="font-size: 13px; letter-spacing: 0.5px">Fri</span>
                                            </label>
                                        </div>
                                        <div class="col-lg-1 col-md-1 col-xs-1" >
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" style="width:20px;height:20px;" name="sat_box" >
                                                <span style="font-size: 13px; letter-spacing: 0.5px">Sat</span>
                                            </label>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-xs-12" style="text-align: center" >
                                            <label style="color:red" name="days_sched_error" ></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-grpup">
                                    <div class="table-responsive " >
                                        <table id="" class="table table-striped table-bordered table-hover" >
                                            <thead style="background-color:#126B95;color:white;text-align: center;letter-spacing: 0.7px">
                                                <tr>
                                                    <td colspan="2"> 
                                                        <span style="color:yellow">Date range</span>
                                                    </td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="input-group date" id="cs_from_datein" style="color:black">
                                                            <div class="input-group-addon">
                                                                From
                                                            </div>
                                                            <input type="date" class="form-control pull-right" name="sched_datein"  >
                                                        </div>
                                                        <label style="color:red" name="sched_datein_error" ></label>
                                                    </td>
                                                    <td>
                                                        <div class="input-group date" id="cs_from_datein" style="color:black">
                                                            <div class="input-group-addon">
                                                                To
                                                            </div>
                                                            <input type="date" class="form-control pull-right" name="sched_dateout"  >
                                                        </div>
                                                        <label style="color:red" name="sched_dateout_error" ></label>
                                                    </td>
                                                </tr>  
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-grpup">
                                    <div class="table-responsive " >
                                        <table id="" class="table table-striped table-bordered table-hover" >
                                            <thead style="background-color:#126B95;color:white;text-align: center;letter-spacing: 0.7px">
                                                <tr>
                                                    <td colspan="2"> 
                                                        <span style="color:yellow">Time</span>
                                                    </td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="input-group date" id="cs_from_datein" style="color:black">
                                                            <div class="input-group-addon">
                                                                In
                                                            </div>
                                                            <input type="time" class="form-control pull-right" name="sched_timein"  >
                                                        </div>
                                                        <label style="color:red" name="sched_timein_error" ></label>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                           
                                                            <div class="col-lg-6">
                                                                <div class="input-group date" id="cs_from_datein" style="color:black;width:50%">
                                                                    <div class="input-group-addon">
                                                                        Out
                                                                    </div>
                                                                    <input type="time" class="form-control pull-right" name="sched_timeout"  >
                                                                </div>
                                                                <label style="color:red" name="sched_timeout_error" ></label>
                                                            </div>
                                                             <div class="col-lg-5">
                                                                <select  class="form-control" name="day_restrict">
                                                                    <option value="1">Same Day</option>
                                                                    <option value="2">Next Day</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>  
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            <div class="modal-footer">
                <button class="btn pull-right  "  name="save_schedule" style="background-color:#3ED03E;">Save</buton>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>