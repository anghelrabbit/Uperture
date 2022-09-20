<div class="row">   
</div>
<div class="box-body" style="max-width: 100%;  overflow: auto;"> 
    <button type='button' class='btn  btn-fw  btn-block'  name="add_cs" style="background-color:#3ED03E;color:white;letter-spacing: 1px"><b>Request Change Schedule</b></button>
    <br> 
    <table id="my_cs_table" class="table table-striped table-bordered" style="width:100%;">
        <thead style="background-color:#2692D0;color:white;">
            <tr>
                <th ></th>
                <th ></th>
                <th style="white-space:nowrap;padding-right: 70px">Date Requested</th>
                <th style="white-space:nowrap;padding-right: 70px">Head Approval</th>
                <th style="white-space:nowrap;padding-right: 70px">HR Approval</th>
                <th style="white-space:nowrap;padding-right: 70px">Category</th>
                <th style="white-space:nowrap;padding-right: 70px">From shift</th>
                <th style="white-space:nowrap;padding-right: 70px">To shift</th>
            </tr>
        </thead>
    </table>
</div>
<?php $this->load->view('pages/modal/modal_cs/modal_cs'); ?>           
<?php $this->load->view('pages/modal/modal_reliever/modal_reliever'); ?>           