<div class="row">   
</div>
<!--<button type='button' class='btn  btn-fw  btn-block'  name="add_employee_schedule" style="background-color:#3ED03E;color:white;letter-spacing: 1px"><b>Add Member To Payroll</b></button>-->
<div class="box-body" style="max-width: 100%;  overflow: auto;"> 
    <br> 
    <table id="schedule_table" class="table table-striped table-bordered" style="width:100%;">
        <thead style="background-color:#2692D0;color:white;">
            <tr>

                <th style="white-space:nowrap;padding-right: 70px" rowspan="2">Action</th>
                <th style="white-space:nowrap;padding-right: 70px"  rowspan="2">Date Requested</th>
                <th style="white-space:nowrap;padding-right: 70px"  rowspan="2">Member</th>
                <th style="white-space:nowrap;padding-right: 70px"  rowspan="2">Reimbursement</th>
                <th style="white-space:nowrap;padding-right: 70px"  rowspan="2">Amount in Php</th>
                <th style="white-space:nowrap;padding-right: 70px;text-align:center" colspan="2">Payment Mode</th>
                <th style="white-space:nowrap;padding-right: 70px;text-align:center" colspan="2">Receipt</th>

            </tr>

        </thead>

        <tr>

            <th style="white-space:nowrap;padding-right: 70px" rowspan="2">
                <button class="btn" onclick="gotoPaypal()"  style="background-color:#3ED03E;color:white"><i class="fa fa-check" style="font-size:18px"></i></button>
                <button class="btn" onclick="addMemberToPayroll()"  style="background-color:#e3a71b;color:white"><i class="fa fa-pen" style="font-size:18px"></i></button>
                <button class="btn" onclick=""  style="background-color:#FF392E;color:white"><i class="fa fa-trash" style="font-size:18px"></i></button>
            </th>
            <th style="white-space:nowrap;padding-right: 70px"  rowspan="2">07/30/2022</th>
            <th style="white-space:nowrap;padding-right: 70px"  rowspan="2">Bunny Empeynado</th>
            <th style="white-space:nowrap;padding-right: 70px"  rowspan="2">Laptop</th>
            <th style="white-space:nowrap;padding-right: 70px"  rowspan="2">46,000</th>
            <th style="white-space:nowrap;padding-right: 70px;text-align:center" colspan="2">Monthly Installment</th>
            <th style="white-space:nowrap;padding-right: 70px;text-align:center" colspan="2">
                  <button class="btn" onclick="viewReimbursementReceipt()"  style="background-color:#3ED03E;color:white"><i class="fa fa-eye" style="font-size:18px"></i></button>
            </th>

        </tr>
    </table>
</div>
