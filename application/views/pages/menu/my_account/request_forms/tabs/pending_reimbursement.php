<div class="row">   
</div>
<div class="box-body" style="max-width: 100%;  overflow: auto;"> 
    <button type='button' class='btn  btn-fw  btn-block'  name="add_reimbursement" style="background-color:#3ED03E;color:white;letter-spacing: 1px"><b>Request Reimbursement</b></button>
    <br> 
    <table id="pending_reimbursement_request_table" class="table table-striped table-bordered" style="width:100%;">
        <thead style="background-color:#2692D0;color:white;">
            <tr>

                <th style="white-space:nowrap;padding-right: 70px">Action</th>
                <th style="white-space:nowrap;padding-right: 70px">Reimbursement Item</th>
                <th style="white-space:nowrap;padding-right: 70px">Amount</th>
                <th style="white-space:nowrap;padding-right: 70px">Payment Mode
                    <select class="form-control" id="select_payment_mode" onchange="fetchPendingReimbursementRequest()">
                        <option value="0">All</option>
                        <option value="1">Full Payment</option>
                        <option value="2">Installment</option>

                    </select>
                </th>
                <th style="white-space:nowrap;padding-right: 70px">Request Date</th>
            </tr>
        </thead>
    </table>
</div>
<?php $this->load->view('pages/modal/modal_reimbursement/modal_reimbursement'); ?>           

 
<?php $this->load->view('pages/modal/modal_reimbursement/modal_view_reimbursement_detail'); ?>           

