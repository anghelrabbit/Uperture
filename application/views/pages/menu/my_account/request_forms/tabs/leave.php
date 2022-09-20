<div class="row">   
</div>
<div class="box-body" style="max-width: 100%;  overflow: auto;"> 
    <button type='button' class='btn  btn-fw  btn-block'  name="add_leave" style="background-color:#3ED03E;color:white;letter-spacing: 1px"><b>Request Leave</b></button>
    <br> 
    <table id="my_leave_table" class="table table-striped table-bordered" style="width:100%;">
        <thead style="background-color:#2692D0;color:white;">
            <tr>
                <th ></th>
                <th ></th>
                <th style="white-space:nowrap;padding-right: 70px">Date Requested</th>
                <th style="white-space:nowrap;padding-right: 70px">Head Approval</th>
                <th style="white-space:nowrap;padding-right: 70px">HR Approval</th>
                <th style="white-space:nowrap;padding-right: 70px">Leave Type</th>
                <th style="white-space:nowrap;padding-right: 70px">Leave Date Range</th>
            </tr>
        </thead>
    </table>
</div>
<?php $this->load->view('pages/modal/modal_leave/modal_leave'); ?>           
     