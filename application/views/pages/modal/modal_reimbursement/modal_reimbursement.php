<div class="modal modal-primary fade" name="reimbursement_modal" tabindex="-1"  role="dialog"  aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color:black;font-weight: bold">X</span></button>
                <div>
                    <span class="" name="reimbursement_title" style="letter-spacing:0.7px;font-size:20px">Reimbursement Form</span>&nbsp;&nbsp;
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
                                                        Full Name
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
                                                        Reimbursement For:
                                                    </div>
                                                    <input id="reimbursement_for" class="form-control ">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" >
                                            <div class="form-group">
                                                <div class="input-group"  style="color:black">
                                                    <div class="input-group-addon">
                                                        Full amount in PHP
                                                    </div>
                                                    <input type="number" id="reimbursement_amount" class="form-control ">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" >
                                            <div class="form-group">
                                                <div class="input-group " style="color:black">
                                                    <div class="input-group-addon">
                                                        Payment Mode
                                                    </div>
                                                    <select id="reimbursement_payment_mode"  onchange="checkIfInstallment()" class="form-control">
                                                        <option  value="1">Full Payment</option>
                                                        <option value="2">Installment</option>
                                                        <!--<option value="0.5">Half Day</option>-->
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" >
                                            <div class="form-group">
                                                <div class="input-group " style="color:black">
                                                    <div class="input-group-addon">
                                                        Regularity
                                                    </div>
                                                    <select id="reimbursement_regularity" disabled class="form-control">
                                                        <option value="1">Monthly</option>
                                                        <option  value="2">Quarterly</option>
                                                        <option value="3">Annually</option>
                                                        <!--<option value="0.5">Half Day</option>-->
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12" >
                                            <div class="form-group">
                                                <div class="input-group " id="" style="color:black">
                                                    <div class="input-group-addon">
                                                        Amount
                                                    </div>
                                                    <input type="number" id="reimbursement_amount_to_pay" disabled class="form-control ">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12" >
                                            <div class="form-group">
                                                <div class="input-group " id="" style="color:black">
                                                    <div class="input-group-addon">
                                                        Upload Receipt
                                                    </div>
                                                    <input type="file" id="reimbursement_receipt" class="form-control ">
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
                <button class="btn pull-right"  name="save_request_reimbursement" onclick="saveReimbursementRequest()" style="background-color:#3ED03E;">Save</buton>
                   
                    <button class="btn pull-right"  name="cancel_leave"  style="background-color:#F8665E;">Request Cancellation</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>