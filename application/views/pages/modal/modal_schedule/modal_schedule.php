<div class="modal modal-primary fade" name="modal_schedule">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color:black;font-weight: bold">X</span></button>
                <h4 class="modal-title" name="cs_title">Member Schedule</h4>
            </div>
            <div class="modal-body">
                <div class="box ">
                    <div class="nav-tabs-custom">
                        <div class="tab-content" style="color:black">
                            <div class="active tab-pane" id="">
                                <form class="form-horizontal" id="form_member_schedule">
                                    <div style="display: flex;  align-items: center;justify-content: center;">
                                        <label style="color:red" name="cs_category_error" class=""></label>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-md-6 col-xs-12">
                                            <div class="form-group ">
                                                <div class="col-lg-12">
                                                    <label for="">Fullname </label>
                                                    <div class="input-group" id="" style="color:black">
                                                        <input type="text" class="form-control" name="member_name"   readonly> 
                                                    </div>
                                                    <label style="color:red" id="member_error" class="hidden">Member required.</label>
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
                                                                    <span style="color:yellow">FROM (date and time-in)</span>
                                                                </td>
                                                                <td colspan="2"> 
                                                                    <span style="color:yellow">To (date and time-out)</span>
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
                                                                        <input type="date" class="form-control pull-right" name="member_timein_date"  >
                                                                    </div>
                                                                    <label style="color:red" name="member_timein_date_error" ></label>
                                                                </td>
                                                                <td style='width:170px;min-width: 170px'>
                                                                    <div class="input-group">
                                                                        <div class="input-group-addon">
                                                                            <i class="">In</i>
                                                                        </div>
                                                                        <input type="time" class="form-control pull-right " name="member_timein_time"  >
                                                                    </div>
                                                                    <label style="color:red" name="member_timein_time_error" ></label>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group" id="cs_from_dateout" style="color:black">
                                                                        <div class="input-group-addon">
                                                                            <i class="fa fa-calendar"></i>
                                                                        </div>
                                                                        <input type="date" class="form-control pull-right" name="member_timeout_date" data-date-container='member_timeout_date' data-provide='datepicker'  >
                                                                    </div>
                                                                    <label style="color:red;" name="member_timeout_date_error" ></label>
                                                                </td>
                                                                <td style='width:170px;min-width: 170px'>
                                                                    <div class="input-group ">
                                                                        <div class="input-group-addon">
                                                                            <i class="">Out</i>
                                                                        </div>
                                                                        <input  type="time" class="form-control pull-right " name="member_timeout_time"  >
                                                                    </div>
                                                                    <label style="color:red" name="member_timeout_time_error" ></label>
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
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn pull-right"  name="save_schedule_edit" style="background-color:#3ED03E;">Save</buton>
            </div>
        </div>
    </div>
</div>