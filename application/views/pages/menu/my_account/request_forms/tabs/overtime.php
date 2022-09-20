
<div class="row">   
</div>
<div class="box-body" style="max-width: 100%;  overflow: auto;"> 
    <button type='button' class='btn  btn-fw  btn-block'  name="add_overtime" style="background-color:#3ED03E;color:white;letter-spacing: 1px"><b>Request Overtime</b></button>
    <br> 
    <table id="my_overtime_table" class="table table-striped table-bordered" style="width:100%;">
        <thead style="background-color:#2692D0;color:white;">
            <tr>
                <th   ></th>
                <th ></th>
                <th style="white-space:nowrap;padding-right: 70px">Date Requested</th>
                <th  style="white-space:nowrap;padding-right: 70px">Head Approval</th>
                <th style="white-space:nowrap;padding-right: 70px">HR Approval</th>
                <th  style="white-space:nowrap;padding-right: 70px">Type</th>
                <th  style="white-space:nowrap;padding-right: 70px">Work Schedule</th>
                <th  style="white-space:nowrap;padding-right: 70px">Overtime</th>
            </tr>
        </thead>
    </table>
</div>

<?php $this->load->view('pages/modal/modal_overtime/modal_overtime'); ?>           


<!--MAGAMIT NI FOR FUTURE USE KABAHIN NI ROWSPAN AND COLSPAN-->

<!--<table name="my_overtime_table" class="table table-striped table-bordered" style="width:100%;">
        <thead style="background-color:#2692D0;color:white;">
            <tr>
                <th  rowspan="2"  ></th>
                <th  rowspan="2" ></th>
                <th rowspan="2" style="white-space:nowrap;padding-right: 70px">Date Requested</th>
                <th rowspan="2" style="white-space:nowrap;padding-right: 70px">Head Approval</th>
                <th rowspan="2" style="white-space:nowrap;padding-right: 70px">HR Approval</th>
                <th rowspan="2" style="white-space:nowrap;padding-right: 70px">Type</th>
                <th rowspan="2" style="white-space:nowrap;padding-right: 70px">Work Schedule</th>
                <th rowspan="2" style="white-space:nowrap;padding-right: 70px">Overtime</th>
                <th rowspan="1" colspan="2" style="white-space:nowrap;padding-right: 70px">
                    asd
                    
                </th>
            </tr>
            <tr>
            
                <th rowspan="1">asd</th>
                <th rowspan="1">asdas</th>
            </tr>
        </thead>
    </table>-->