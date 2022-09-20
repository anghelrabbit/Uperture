<div class="modal modal-primary fade" name="leave_modal" tabindex="-1"  role="dialog"  aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color:black;font-weight: bold">X</span></button>
                <div >
                    <span class="" name="leave_title" style="letter-spacing:0.7px;font-size:20px">Leave Form</span>&nbsp;&nbsp;
                    <!--<span class="btn badge" name='leave_helpdesk' style="background-color:#3ED03E;height:25px;width:25px;font-size:15px;margin-top: -7px">?</span>-->
                </div>
            </div>
            <div class="modal-body">
                <div class="box ">
                    <div class="box-header with-border ">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#leave_details" data-toggle="tab" style="font-weight: bold;">Details</a></li>
                            <li class=""><a  href="#leave_signatory" name="leave_signatory" data-toggle="tab" style="font-weight: bold;"> Signatory  </a> </li>

                        </ul>   
                    </div>
                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="active tab-pane" id="leave_details">
                                <form  id="leave_form">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12" >
                                            <div class="form-group">
                                                <div class="input-group" style="color:black">
                                                    <div class="input-group-addon">
                                                        Company
                                                    </div>
                                                    <input type="text" class="form-control" name="leave_compname" name="" value="<?php echo $this->session->userdata('compname') ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" >
                                            <div class="form-group">
                                                <div class="input-group"  style="color:black">
                                                    <div class="input-group-addon">
                                                        Fullname
                                                    </div>

                                                    <input type="text" class="form-control" name="leave_empname" name="" value="<?php echo $this->session->userdata('empname') ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" >
                                            <div class="form-group">
                                                <div class="input-group "  style="color:black">
                                                    <div class="input-group-addon">
                                                        Position
                                                    </div>
                                                    <input type="text" class="form-control pull-right" name="leave_jobpos"  value="<?php echo $this->session->userdata('jobposition') ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" >
                                            <div class="form-group">
                                                <div class="input-group" style="color:black">
                                                    <div class="input-group-addon">
                                                        Type of leave:
                                                    </div>
                                                    <select class="form-control form-control-lg" name="leave_type"  >
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" >
                                            <div class="form-group">
                                                <div class="input-group"  style="color:black">
                                                    <div class="input-group-addon">
                                                        If others:
                                                    </div>
                                                    <input name="leave_ifothers" class="form-control " readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" >
                                            <div class="form-group">
                                                <div class="input-group " style="color:black">
                                                    <div class="input-group-addon">
                                                        Day Category
                                                    </div>
                                                    <select name="leave_day_category" name="leaveday"  class="form-control">
                                                        <option  value="1">Whole Day</option>
                                                        <option value="2">Specify Dates</option>
                                                        <!--<option value="0.5">Half Day</option>-->
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" >
                                            <div class="form-group">
                                                <div class="input-group " id="" style="color:black">
                                                    <div class="input-group-addon">
                                                        Pay & Leave
                                                    </div>
                                                    <select  name="leave_payment_type" class="form-control">
                                                        <option value="0">Without Pay</option>
                                                        <option value="1">With Pay</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6  col-xs-12" >
                                            <div class="form-group">
                                                <div class="col-lg-12">
                                                    <label style="color:black">From: </label>
                                                    <input  class="form-control" name="leave_datefrom" type="date" value="<?php echo date('Y-m-d') ?>" data-toggle="tooltip" title="Dates will be disabled if you dont have an available workday or you already have an approved leave on those dates.">
                                                    <label style="color:red" name="leave_datefrom_error"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" >
                                            <div class="form-group">
                                                <div class="col-lg-12 ">
                                                    <label style="color:black">To:</label>
                                                    <input  class="form-control"  name="leave_dateto" type="date"   value=""  >
                                                    <label style="color:red" name="leave_dateto_error"></label>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row" >
                                        <div class="col-lg-12" >
                                            <div class="form-group">
                                                <div style="height: 100%;max-height: 150px;  overflow: auto;" name="leave_dates" >

                                                </div>
                                                <div class="input-group " style="color:black">
                                                    <div class="input-group-addon">
                                                        <span class="">Total:</span>
                                                    </div>
                                                    <input type="number" class="form-control hidden"  style="text-align: right"  readonly  name="previous_total" />
                                                    <input type="number" class="form-control pull-right"  style="text-align: right"  readonly  name="total_days" />
                                                    <div class="input-group-addon">
                                                        <span class="" >Day/s</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" >
                                        <div class="col-md-12 ">
                                            <div class="form-group ">
                                                <label style="color:black">Reason</label>
                                                <textarea class="form-control"  name="leave_reason" value=" " rows="3" style="border:solid lightgrey" ></textarea>
                                                <!--<input type="hidden" class="form-control"  name="numdays">-->
                                                <input type="text" id="txtreasonlength" value="0/300" style="font-size: 10px;  background: transparent;  border: 0;outline: 0;margin-left:90%" disabled />
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>

                            <div class="tab-pane" id="leave_signatory">

                            </div>
                        </div>

                    </div>

                </div>
                <div name="leave_approval_content">

                </div>
            </div>
            <div class="modal-footer">
                <button class="btn pull-right  hidden"  name="save_leave" style="background-color:#3ED03E;">Save</buton>
                    <button class="btn pull-right" name="update_leave"  style="background-color:#F87D42;">Update</button>
                    <button class="btn pull-right"  name="remove_leave"  style="background-color:#F8665E;">Remove</button>
                    <button class="btn pull-right"  name="cancel_leave"  style="background-color:#F8665E;">Request Cancellation</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>