
<div class="modal modal-primary fade" name="modal_view_reimbursement_detail">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" >Reimbursement Details</h4>
            </div>
            <div class="modal-body">
                <div class="box ">
                    <div class="box-header with-border ">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#employees" data-toggle="tab" style="font-weight: bold;">Reimbursement</a></li>
                        </ul>   
                    </div>
                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="active tab-pane" id="reimbursement_details">
                                <form  id="add_account_form" autocomplete="off">
                                    <input id="txtaction" type="hidden" readonly/>
                                    <input id="txtprofileno" type="hidden" readonly/>
                                    <div class="row">

                                        <div class="col-lg-8 col-md-8 col-sm-8" >
                                            <div class="form-group">
                                                <div class="input-group"  style="color:black">
                                                    <div class="input-group-addon">
                                                        Requested By:
                                                    </div>

                                                    <input type="text" class="form-control" name="acc_empname" name="" value="<?php echo $this->session->userdata('empname') ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4" >
                                            <div class="form-group">
                                                <div class="input-group"  style="color:black">
                                                    <div class="input-group-addon">
                                                        Request Date:
                                                    </div>

                                                    <input type="text" class="form-control" name="reim_request_date"  id="reim_request_date" readonly>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-lg-8 col-md-6 col-sm-6" >
                                            <div class="form-group">
                                                <div class="input-group"  style="color:black">
                                                    <div class="input-group-addon">
                                                        Reimbursement For:
                                                    </div>
                                                    <input type="text" class="form-control" name="reim_item" id="reim_item"  readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4" >
                                            <div class="form-group">
                                                <div class="input-group"  style="color:black">
                                                    <div class="input-group-addon">
                                                        Payment Mode:
                                                    </div>

                                                    <input type="text" class="form-control" name="reim_payment_mode" id="reim_payment_mode" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-8 col-md-8 col-sm-8" >
                                            <div class="form-group">
                                                <div class="input-group"  style="color:black">
                                                   <img class="img-responsive" src="assets/images/receipt-sample.jpg" alt="Receipt">
                                                </div>
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



        </div>

        <div class="modal-footer">
            <button class="btn btn-success pull-right" style="background-color: #3ED03E; color:white" onclick="updateLeaveCredits()" >Save</button>
        </div>
    </div>
    <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
