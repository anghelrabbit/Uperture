<div class="modal modal-primary fade" name="modal_overtime_notif" tabindex="-1"  role="dialog"  aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color:black;font-weight: bold">X</span></button>
                <h4 class="modal-title" name="overtime_title_notif">Overtime Form</h4>
            </div>
            <div class="modal-body">
                <div class="box ">
                    <div class="box-header with-border ">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#overtime_details_notif" data-toggle="tab" name="overtime_details" style="font-weight: bold;">Details</a></li>

                        </ul>   
                    </div>
                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="active tab-pane" id="overtime_details_notif">
                                <form  id="overtime_form_notif">
                                    <div class="row">
                                        <div class="col-lg-3" id=''>
                                            <div class="form-group ">
                                                <label style="color:black" for="">Overtime Type</label>
                                                <select class="form-control" name="overtime_type_notif" >
                                                </select>
                                                   <label name="overtime_type_notif_error" style="color:red"></label>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 ">
                                            <div class="form-group ">
                                                <label style="color:black" for="">Employee name</label>
                                                <input type="text" class="form-control" name="ovetime_employeename_notif"  value="<?php echo $this->session->userdata('empname') ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-5 col-md-6" id=''>
                                            <div class="form-group ">
                                                <label style="color:black" for="">Company</label>

                                                <input type="text" class="form-control" name="ovetime_company_notif" value="<?php echo $this->session->userdata('compname') ?>" readonly>
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
                                                                            <input type="date"  class="form-control " name="overtime_worksched_datein_notif"  value="" >
                                                                        </div>
                                                                        <div style="color:red;text-align: center;font-weight: bold" name="overtime_worksched_notif_error"> 

                                                                        </div>
                                                                    </td>
                                                                    <td style='width:170px;min-width: 170px'>
                                                                        <div class="input-group">
                                                                            <div class="input-group-addon">
                                                                                <i class="">In</i>
                                                                            </div>
                                                                            <input type="text" class="form-control pull-right " name="overtime_worksched_timein_notif"  readonly>
                                                                        </div>
                                                                    </td>
                                                                    <td> 
                                                                        <div class="input-group date" >
                                                                            <div class="input-group-addon">
                                                                                <i class="fa fa-calendar"></i>
                                                                            </div>
                                                                            <input type="date" class="form-control pull-right" name="overtime_worksched_dateout_notif"  readonly>
                                                                        </div>
                                                                    </td>
                                                                    <td style='width:170px;min-width: 170px'>
                                                                        <div class="input-group date">
                                                                            <div class="input-group-addon">
                                                                                <i class="">Out</i>
                                                                            </div>
                                                                            <input type="text" class="form-control pull-right " name="overtime_worksched_out_notif"  readonly>
                                                                        </div>
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
                                                                            <input type="date" class="form-control " name="overtime_actual_datein_notif"  >
                                                                        </div>
                                                                        <label name="overtime_actual_datein_notif_error" style="color:red"></label>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group ">
                                                                            <div class="input-group-addon">
                                                                                <i class="">In</i>
                                                                            </div>
                                                                            <input type="time" class="form-control pull-right" name="overtime_actualin_notif"  >
                                                                        </div>
                                                                        <label name="overtime_actualin_notif_error" style="color:red"></label>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group date"  style="color:black">
                                                                            <div class="input-group-addon">
                                                                                <i class="fa fa-calendar"></i>
                                                                            </div>
                                                                            <input type="date"  class="form-control " name="overtime_actual_dateout_notif" data-toggle="tooltip">
                                                                        </div>
                                                                        <label name="overtime_actual_dateout_notif_error" style="color:red"></label>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group date">
                                                                            <div class="input-group-addon">
                                                                                <i class="">Out</i>
                                                                            </div>
                                                                            <input type="time" class="form-control pull-right " name="overtime_actualout_notif"  onkeyup="timeKeyUp(event, 'undertime_actualout')" onkeydown="timeKeyDown('undertime_actualout')" >
                                                                        </div>
                                                                        <label name="overtime_actualout_notif_error" style="color:red"></label>
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
                                                <textarea rows="4" class="form-control" name="overtime_reason_notif" name="" style="resize:none"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>

                            
                        </div>

                    </div>

                </div>
             
            </div>
            <div class="modal-footer">
                <span class="btn pull-right "  name="save_overtime_notif" style="background-color:#3ED03E;">Save</span>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>