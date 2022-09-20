<div class="modal modal-primary fade" name="undertime_modal" tabindex="-1"  role="dialog"  aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color:black;font-weight: bold">X</span></button>
                <div >
                    <span class="" name="undertime_title" style="letter-spacing:0.7px;font-size:20px">Undertime Form</span>&nbsp;&nbsp;
                    <!--<span class="btn badge" name='undertime_helpdesk' style="background-color:#3ED03E;height:25px;width:25px;font-size:15px;margin-top: -7px">?</span>-->
                </div>
            </div>
            <div class="modal-body">
                <div class="box ">
                    <div class="box-header with-border ">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#undertime_details" data-toggle="tab" style="font-weight: bold;">Details</a></li>
                            <li class=""><a  href="#undertime_signatory" name="undertime_signatory" data-toggle="tab" style="font-weight: bold;"> Signatory  </a> </li>

                        </ul>   
                    </div>
                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="active tab-pane" id="undertime_details">
                                <form  id="undertime_form">
                                    <div class="row">
                                        <div class="col-lg-3" >
                                            <div class="form-group ">
                                                <label style="color:black" for="">Undertime Type</label>
                                                <select class="form-control" name="undertime_type"  onchange="undertimeTypeRestriction()">
                                                    <option value="0">Time-in</option>
                                                    <option value="1">Time-out</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 ">
                                            <div class="form-group ">
                                                <label style="color:black" for="">Employee name</label>
                                                <input type="text" class="form-control" name="undertime_employeename"  value="<?php echo $this->session->userdata('empname') ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-5" >
                                            <div class="form-group ">
                                                <label style="color:black" for="">Company</label>

                                                <input type="text" class="form-control" name="undertime_company" value="<?php echo $this->session->userdata('compname') ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-grpup">
                                                    <div class="table-responsive " >
                                                        <table id="" class="table table-striped table-bordered table-hover" style="width:100%;" >
                                                            <thead style="background-color:#126B95;color:white;text-align: center;letter-spacing: 0.7px">
                                                                <tr>
                                                                    <td colspan="2"> 
                                                                        <span style="color:yellow;">Work Schedule Time In</span>
                                                                    </td>
                                                                    <td colspan="2"> 
                                                                        <span style="color:yellow">Work Schedule Time Out</span>
                                                                    </td >  
                                                                </tr>
                                                            </thead>
                                                            <tbody >
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group date">
                                                                            <div class="input-group-addon">
                                                                                <i class="fa fa-calendar"></i>
                                                                            </div>
                                                                            <input type="date"  class="form-control " name="undertime_worksched_datein"  value="" >
                                                                        </div>
                                                                        <div style="color:red;text-align: center;font-weight: bold" name="undertime_worksched_error"> 

                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <div class="input-group-addon">
                                                                                <i class="">In</i>
                                                                            </div>
                                                                            <input type="time" class="form-control pull-right " name="undertime_worksched_timein"  readonly>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group date" >
                                                                            <div class="input-group-addon">
                                                                                <i class="fa fa-calendar"></i>
                                                                            </div>
                                                                            <input type="date" class="form-control pull-right" name="undertime_worksched_dateout"  readonly>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group date">
                                                                            <div class="input-group-addon">
                                                                                <i class="">Out</i>
                                                                            </div>
                                                                            <input type="time" class="form-control pull-right " name="undertime_worksched_out"  readonly>
                                                                        </div>
                                                                    </td>
                                                                </tr>  
                                                            </tbody>
                                                        </table>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-grpup">
                                                    <div class="table-responsive">
                                                        <table  class="table table-striped table-bordered table-hover" style="width:100%;">
                                                            <thead style="background-color:#126B95;color:white;text-align: center;letter-spacing: 0.7px">
                                                                <tr>
                                                                    <td colspan="2"> 
                                                                        <span style="color:yellow"> Actual Time in</span>
                                                                    </td>
                                                                    <td colspan="2"> 
                                                                        <span style="color:yellow">Actual Time out</span>
                                                                    </td >  

                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group date">
                                                                            <div class="input-group-addon">
                                                                                <i class="fa fa-calendar"></i>
                                                                            </div>
                                                                            <input type="date" class="form-control " name="undertime_actual_datein"  data-toggle="tooltip">
                                                                            <input type="date" class="form-control hidden" name="undertime_datein_disable" readonly>
                                                                        </div>
                                                                        <label name="undertime_actual_datein_error" style="color:red"></label>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group ">
                                                                            <div class="input-group-addon">
                                                                                <i class="">In</i>
                                                                            </div>
                                                                            <input type="time" class="form-control pull-right" name="undertime_actualin"  >
                                                                            <input type="time" class="form-control hidden" name="undertime_actualin_disable" readonly>
                                                                        </div>
                                                                        <label name="undertime_actualin_error" style="color:red"></label>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group date"  style="color:black">
                                                                            <div class="input-group-addon">
                                                                                <i class="fa fa-calendar"></i>
                                                                            </div>
                                                                            <input type="date"  class="form-control " name="undertime_actual_dateout" data-toggle="tooltip">
                                                                            <input type="date"  class="form-control hidden" name="undertime_dateout_disable" readonly>
                                                                        </div>
                                                                        <label name="undertime_actual_dateout_error" style="color:red"></label>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group date">
                                                                            <div class="input-group-addon">
                                                                                <i class="">Out</i>
                                                                            </div>
                                                                            <input type="time" class="form-control pull-right " name="undertime_actualout"  onkeyup="timeKeyUp(event, 'undertime_actualout')" onkeydown="timeKeyDown('undertime_actualout')" >
                                                                            <input type="time" class="form-control hidden" name="undertime_actualout_disable"   readonly>
                                                                        </div>
                                                                        <label name="undertime_actualout_error" style="color:red"></label>
                                                                    </td>
                                                                </tr>  
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                    <div class="row" >
                                        <div class="col-md-12 ">
                                            <div class="form-group ">
                                                <label style="color:black" for="">Reason:</label>
                                                <textarea rows="4" class="form-control" name="undertime_reason" name="" style="resize:none"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>

                            <div class="tab-pane" id="undertime_signatory">

                            </div>
                        </div>

                    </div>

                </div>
                <div name="undertime_approval_content">

                </div>
            </div>
            <div class="modal-footer">
                <button class="btn pull-right  hidden"  name="save_undertime" style="background-color:#3ED03E;">Save</buton>
                    <button class="btn pull-right" name="update_undertime"  style="background-color:#F87D42;">Update</button>
                    <button class="btn pull-right"  name="remove_undertime"  style="background-color:#F8665E;">Remove</button>
                    <button class="btn pull-right"  name="cancel_undertime"  style="background-color:#F8665E;">Request Cancellation</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>