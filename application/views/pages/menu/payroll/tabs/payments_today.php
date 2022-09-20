<div class="row">   
</div>
<button type='button' onclick="addMemberToPayroll()" class='btn  btn-fw  btn-block'  name="btn_add_member_to_payroll" style="background-color:#3ED03E;color:white;letter-spacing: 1px"><b>Add Member To Payroll</b></button>
<div class="box-body" style="max-width: 100%;  overflow: auto;"> 
    <br> 
    <table id="schedule_table" class="table table-striped table-bordered" style="width:100%;">
        <thead style="background-color:#2692D0;color:white;">
            <tr>

                <th style="white-space:nowrap;padding-right: 70px" rowspan="2">Action</th>
                <th style="white-space:nowrap;padding-right: 70px"  rowspan="2">Member</th>
                <th style="white-space:nowrap;padding-right: 70px"  rowspan="2">Integration</th>
                <th style="white-space:nowrap;padding-right: 70px"  rowspan="2">Job Status</th>
                <th style="white-space:nowrap;padding-right: 70px"  rowspan="2">Rate</th>
                <th style="white-space:nowrap;padding-right: 70px;text-align:center" colspan="2">Attendance</th>
                <th style="white-space:nowrap;padding-right: 70px;text-align:center" colspan="2">Overtime</th>
                <th style="white-space:nowrap;padding-right: 70px;text-align:center" colspan="2">Reimbursements</th>
                <th style="white-space:nowrap;padding-right: 70px;text-align:center" colspan="2">Bonuses</th>
                <th style="white-space:nowrap;padding-right: 70px;text-align:center" colspan="2">Total</th>
            </tr>
          
        </thead>
        <tr>

            <th style="white-space:nowrap;padding-right: 70px" rowspan="2">
                <button class="btn" onclick="gotoPaypal()"  style="background-color:#3ED03E;color:white"><i class="fa fa-check" style="font-size:18px"></i></button>
                <button class="btn" onclick="addMemberToPayroll()"  style="background-color:#e3a71b;color:white"><i class="fa fa-pen" style="font-size:18px"></i></button>
                <button class="btn" onclick=""  style="background-color:#FF392E;color:white"><i class="fa fa-trash" style="font-size:18px"></i></button>
                
                
            </th>
                <th style="white-space:nowrap;padding-right: 70px"  rowspan="2">Bunny Empeynado</th>
                <th style="white-space:nowrap;padding-right: 70px"  rowspan="2">Paypal</th>
                <th style="white-space:nowrap;padding-right: 70px"  rowspan="2">Regular/Full Time</th>
                <th style="white-space:nowrap;padding-right: 70px"  rowspan="2">420</th>
                <th style="white-space:nowrap;padding-right: 70px;text-align:center" colspan="2">420</th>
                <th style="white-space:nowrap;padding-right: 70px;text-align:center" colspan="2">72</th>
                <th style="white-space:nowrap;padding-right: 70px;text-align:center" colspan="2">20</th>
                <th style="white-space:nowrap;padding-right: 70px;text-align:center" colspan="2">0</th>
                <th style="white-space:nowrap;padding-right: 70px;text-align:center" colspan="2">512</th>
            </tr>
    </table>
</div>


<?php $this->load->view('pages/modal/modal_payroll/modal_add_member_to_payroll'); ?>