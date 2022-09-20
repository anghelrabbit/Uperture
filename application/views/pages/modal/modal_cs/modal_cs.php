<div class="modal modal-primary fade" name="modal_cs">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color:black;font-weight: bold">X</span></button>
                <h4 class="modal-title" name="cs_title">Change Schedule</h4>
            </div>
            <div class="modal-body">
                <div class="box ">
                    <div class="box-header with-border ">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#cs_details" data-toggle="tab" style="font-weight: bold;">Details</a></li>
                            <li class=""><a  href="#cs_signatory" name="cs_signatory" data-toggle="tab" style="font-weight: bold;"> Signatory  </a> </li>

                        </ul>   
                    </div>
                    <div class="nav-tabs-custom">
                        <div class="tab-content" style="color:black">
                            <div class="active tab-pane" id="cs_details">
                                <form class="form-horizontal" >
                                    <div style="display: flex;  align-items: center;justify-content: center;">
                                        <label style="color:red" name="cs_category_error" class=""></label>
                                    </div>
                                    <div class="form-group" style="border-color: grey;border-style: dashed;border-left: 0;border-width: 1px; border-right: 0;margin-left: 1%; margin-right: 1%;" >

                                        <div class="col-lg-3 col-md-3 col-xs-3">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" style="width:20px;height:20px;" name="shiftchange" > 
                                                <span style="font-size: 13px;letter-spacing: 0.5px">Shift Change</span>
                                            </label>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-xs-3" >
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" style="width:20px;height:20px;" name="straightduty"  > 
                                                <span style="font-size: 13px;letter-spacing: 0.5px"> Straight Duty</span>
                                            </label>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-xs-3" >
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" style="width:20px;height:20px;" name="canceldayoff"  > 
                                                <span style="font-size: 13px;letter-spacing: 0.5px">Cancel Day-off</span>
                                            </label>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-xs-3" >
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" style="width:20px;height:20px;" name="changedayoff" >
                                                <span style="font-size: 13px; letter-spacing: 0.5px">Change Day-off</span>
                                            </label>
                                        </div>

                                    </div>
                                    <div class="form-group"  >

                                        <div class="col-lg-6 col-md-6 col-xs-12">
                                            <div class="input-group" id="" style="color:black">
                                                <div class="input-group-addon">
                                                    <i class="">Date Requested</i>
                                                </div>
                                                <input type="text"  class="form-control" name="cs_date_filed" readonly value="<?php echo date('m/d/Y') ?>"> 
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-xs-12">
                                            <div class="input-group" id="" style="color:black">
                                                <div class="input-group-addon">
                                                    <i class="">Company</i>
                                                </div>
                                                <input type="text"  class="form-control" name="cs_company" readonly value="<?php echo $this->session->userdata('compname') ?>"> 
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-md-6 col-xs-12">
                                            <div class="form-group ">
                                                <div class="col-lg-12">
                                                    <label for="">Employee name</label>
                                                    <input type="text" class="form-control" name="cs_employeename"  value="<?php echo $this->session->userdata('empname') ?>" readonly>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-md-6 col-md-6 col-xs-12">
                                            <div class="form-group ">
                                                <div class="col-lg-12">
                                                    <label for="">Reliever </label>
                                                    <div class="input-group" id="" style="color:black">
                                                        <input type="text" class="form-control" name="cs_relievername"   readonly> 
                                                        <div class="input-group-btn">
                                                            <span class="btn" style="background-color:#3ED03E;color:white" name="reliever_btn">Select Reliever</span>
                                                        </div>
                                                    </div>
                                                    <label style="color:red" id="cs_reliever_error" class="hidden">Reliever required.</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-grpup">
                                                <div class="table-responsive " >
                                                    <table id="" class="table table-striped table-bordered table-hover" style="width:100%;">
                                                        <thead style="background-color:#126B95;color:white;text-align: center;letter-spacing: 0.7px">
                                                            <tr>
                                                                <td colspan="2"> 
                                                                    <span style="color:yellow">FROM (shift date and time-in)</span>
                                                                </td>
                                                                <td colspan="2"> 
                                                                    <span style="color:yellow">FROM (shift date and time-out)</span>
                                                                </td >
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <div class="input-group date" id="cs_from_datein" style="color:black">
                                                                        <div class="input-group-addon">
                                                                            <i class="fa fa-calendar"></i>
                                                                        </div>
                                                                        <input type="date" class="form-control pull-right" name="cs_fromshift_datein"  >
                                                                    </div>
                                                                    <label style="color:red" name="cs_fromshift_datein_error" ></label>
                                                                </td>
                                                                <td style='width:170px;min-width: 170px'>
                                                                    <div class="input-group">
                                                                        <div class="input-group-addon">
                                                                            <i class="">In</i>
                                                                        </div>
                                                                        <input type="time" class="form-control pull-right " name="cs_fromshift_timein"  readonly>
                                                                        <input type="text" class="form-control pull-right hidden" name="cs_fromshift_timein_dayoff" value="Day Off" readonly>
                                                                    </div>
                                                                    <label style="color:red" name="cs_fromshift_timein_error" ></label>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group" id="cs_from_dateout" style="color:black">
                                                                        <div class="input-group-addon">
                                                                            <i class="fa fa-calendar"></i>
                                                                        </div>
                                                                        <input type="date" class="form-control pull-right" name="cs_fromshift_dateout" data-date-container='cs_from_dateout' data-provide='datepicker'  readonly>
                                                                    </div>
                                                                    <label style="color:red;" name="cs_fromshift_dateout_error" ></label>
                                                                </td>
                                                                <td style='width:170px;min-width: 170px'>
                                                                    <div class="input-group ">
                                                                        <div class="input-group-addon">
                                                                            <i class="">Out</i>
                                                                        </div>
                                                                        <input  type="time" class="form-control pull-right " name="cs_fromshift_timeout"  readonly>
                                                                        <input  type="text" class="form-control pull-right hidden" name="cs_fromshift_timeout_dayoff" value="Day Off"  readonly>
                                                                    </div>
                                                                    <label style="color:red" name="cs_fromshift_timeout_error" ></label>
                                                            </tr>  
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-grpup">
                                                <div class="table-responsive">
                                                    <table id="reliever_sched_table" class="table table-striped table-bordered table-hover" style="width:100%;">
                                                        <thead style="background-color:#126B95;color:white;text-align: center;letter-spacing: 0.7px">
                                                            <tr>
                                                                <td colspan="2"> 
                                                                    <span style="color:yellow">TO (shift date and time-in)</span>
                                                                </td>
                                                                <td colspan="2"> 
                                                                    <span style="color:yellow">TO (shift date and time-out)</span>
                                                                </td >
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <div class="input-group date" id="cs_to_datein" style="color:black">
                                                                        <div class="input-group-addon">
                                                                            <i class="fa fa-calendar"></i>
                                                                        </div>

                                                                        <input type="date" class="form-control pull-right" name="cs_toshift_datein" >
                                                                    </div>
                                                                    <label style="color:red" name="cs_toshift_datein_error" ></label>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group "  style="color:black">
                                                                        <div class="input-group-addon">
                                                                            <i class="">In</i>
                                                                        </div>
                                                                        <input type="time" class="form-control pull-right " name="cs_toshift_timein"   >
                                                                        <input type="text" class="form-control pull-right hidden" name="cs_toshift_timein_dayoff"  >
                                                                    </div> 
                                                                    <label style="color:red" name="cs_toshift_timein_error" ></label>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group date" id="cs_to_dateout" style="color:black">
                                                                        <div class="input-group-addon">
                                                                            <i class="fa fa-calendar"></i>
                                                                        </div>

                                                                        <input type="date" class="form-control pull-right" name="cs_toshift_dateout" >
                                                                    </div>
                                                                    <label style="color:red" name="cs_toshift_dateout_error" ></label>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group date" >
                                                                        <div class="input-group-addon">
                                                                            <i class="">Out</i>
                                                                        </div>
                                                                        <input type="time" class="form-control pull-right " name="cs_toshift_timeout"  >
                                                                        <input type="text" class="form-control pull-right hidden" name="cs_toshift_timeout_dayoff"    >
                                                                    </div>
                                                                    <label style="color:red" name="cs_toshift_timeout_error" ></label>
                                                            </tr>  
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>


                                    </div>


                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <div class="form-group ">
                                                <div class="col-lg-12">
                                                    <label for="">Reason:</label>
                                                    <textarea rows="4" class="form-control" name="cs_reason"  style="resize:none"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                </form>
                            </div>
                            <div class="tab-pane" id="cs_signatory">

                            </div>
                        </div>
                    </div>
                </div>
                <div name="cs_approval_content">

                </div>
            </div>
            <div class="modal-footer">
                <button class="btn pull-right  hidden"  name="save_cs" style="background-color:#3ED03E;">Save</buton>
                    <button class="btn pull-right" name="update_cs"  style="background-color:#F87D42;">Update</button>
                    <button class="btn pull-right"  name="remove_cs"  style="background-color:#F8665E;">Remove</button>
                    <button class="btn pull-right"  name="cancel_cs"  style="background-color:#F8665E;">Request Cancellation</button>
            </div>
        </div>
    </div>
</div>