<div class="modal modal-primary fade" name="modal_add_account_details" tabindex="-1"  role="dialog"  aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color:black;font-weight: bold">X</span></button>
                <div>
                    <span class="" name="account_title" style="letter-spacing:0.7px;font-size:20px">Please Add This Following Details</span>&nbsp;&nbsp;
                    <!--<span class="btn badge" name='leave_helpdesk' style="background-color:#3ED03E;height:25px;width:25px;font-size:15px;margin-top: -7px">?</span>-->
                </div>
            </div>
            <div class="modal-body">
                <div class="box ">
                    <div class="box-header with-border ">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#leave_details" data-toggle="tab" style="font-weight: bold;">Details</a></li>
                            <!--<li class=""><a  href="#leave_signatory" name="leave_signatory" data-toggle="tab" style="font-weight: bold;"> Signatory  </a> </li>-->

                        </ul>   
                    </div>
                    
                    
                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="active tab-pane" id="reimbursement_details">
                                <form  id="add_account_form">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12" >
                                            <div class="form-group">
                                                <div class="input-group" style="color:black">
                                                    <div class="input-group-addon">
                                                        Company
                                                    </div>
                                                    <input type="text" class="form-control" name="acc_compname"  value="<?php echo $this->session->userdata('compname') ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12" >
                                            <div class="form-group">
                                                <div class="input-group"  style="color:black">
                                                    <div class="input-group-addon">
                                                        Approved By:
                                                    </div>

                                                    <input type="text" class="form-control" name="acc_empname" name="" value="<?php echo $this->session->userdata('empname') ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12" >
                                            <div class="form-group">
                                                <div class="input-group"  style="color:black">
                                                    <div class="input-group-addon">
                                                        Date Officially Hired:
                                                    </div>
                                                    <input type="date" class="form-control" name="acc_doh">


                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" >
                                            <div class="form-group">
                                                <div class="input-group "  style="color:black">
                                                    <div class="input-group-addon">
                                                        Name
                                                    </div>
                                                       <input type="text" class="form-control" name="acc_emp_name">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" >
                                            <div class="form-group">
                                                <div class="input-group" style="color:black">
                                                    <div class="input-group-addon">
                                                        Email Address
                                                    </div>
                                                    <input type="text" class="form-control" name="acc_email_add">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-6 col-md-6 col-sm-6" >
                                            <div class="form-group">
                                                <div class="input-group "  style="color:black">
                                                    <div class="input-group-addon">
                                                        Department
                                                    </div>
                                                    <select class="form-control form-control-sm" id="request_division" name="" onchange="tabCategory('');">

                                                        <option value="DIV-001">Business Development</option>
                                                        <option value="DIV-002">Home Ambassadors</option>
                                                        <option value="DIV-003">Book That Condo</option>
                                                        <option value="DIV-004">Royalty Cleaning</option>
                                                        <option value="DIV-005">Customer Service</option>
                                                        <option value="DIV-006">Night Calls</option>
                                                        <option value="DIV-007">Owner Experience</option>
                                                        <option value="DIV-008">Accounting</option>
                                                        <option value="DIV-009">Tahoe Truckee</option>
                                                        <option value="DIV-010">Training Development</option>
                                                        <option value="DIV-011">Marketing</option>
                                                    </select>    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" >
                                            <div class="form-group">
                                                <div class="input-group" style="color:black">
                                                    <div class="input-group-addon">
                                                        Position
                                                    </div>
                                                    <select class="form-control form-control-sm" id="request_division" name="" onchange="tabCategory('');">
                                                        <option value="DIV-011">Team Leader</option>
                                                        <option value="DIV-008">Member</option>

                                                    </select>    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" >
                                            <div class="form-group">
                                                <div class="input-group"  style="color:black">
                                                    <div class="input-group-addon">
                                                        Job Status
                                                    </div>
                                                    <select class="form-control form-control-sm" id="request_division" name="" onchange="tabCategory('');">
                                                        <option value="DIV-011">Regular/Full Time</option>
                                                        <option value="DIV-008">Part Time</option>

                                                    </select> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" >
                                            <div class="form-group">
                                                <div class="input-group " style="color:black">
                                                    <div class="input-group-addon">
                                                        Pay Period
                                                    </div>
                                                    <select name="reimbursement_payment_mode" class="form-control">
                                                        <option  value="1">Monthly</option>
                                                        <option value="2">Twice a month</option>
                                                        <!--<option value="0.5">Half Day</option>-->
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12" >
                                            <div class="form-group">
                                                <div class="input-group " id="" style="color:black">
                                                    <div class="input-group-addon">
                                                        Referral Person
                                                    </div>
                                                    <select name="reimbursement_payment_mode" class="form-control">
                                                        <option  value="1">None</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12" >
                                            <div class="form-group">
                                                <div class="input-group " id="" style="color:black">
                                                    <div class="input-group-addon">
                                                        Resume
                                                    </div>
                                                    <input type="file" class="form-control" id="worksched_in">
                                                </div>
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
                <button class="btn pull-right"  name="save_leave" style="background-color:#3ED03E;">Save</buton>
               
                <button class="btn pull-left"  name="cancel_leave" onclick="closeModal()"  style="background-color:#F8665E;">Request Cancellation</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>