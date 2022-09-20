

<div class="col-md-7 col-lg-4">
    <input type="hidden" name="profileno" value="<?php echo $profileno ?>"/>
    <input type="hidden" name="company" value="<?php echo $company ?>"/>
    <div class="box bg-gray " style="border-top-color:#2692D0" >
        <div class="box-header with-border " >
            <h3 class="box-title " style="font-weight:bold;letter-spacing:1px">Work Schedule</h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" >
                <div class="form-group">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-group date " id="" style="color:black">
                            <div class="input-group-addon">
                                <i class="">From</i>
                            </div>
                            <input type="date" class="form-control pull-right " id="worksched_in" onchange="setupPayPeriod()" onkeyup="$('#datefiled_in').val('')" value="<?php echo date('Y-m-d') ?>">
                        </div>

                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-group date " id="" style="color:black">
                            <div class="input-group-addon">
                                <i class="">To</i>
                            </div>
                            <input type="date" class="form-control pull-right " id="worksched_out"  readonly>
                        </div>
                    </div>
                    <!--        <div class="col-md-5">
                    
                                <input type="month" class="form-control "  id="payslipdate" name="payslipdate" value="<?= $thismonth ?>" onchange="payslip.payslip();"/>
                            </div>-->
                </div>  
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="worksched_table" class="table  table-bordered" style="background-color:white;width:100%">
                                <thead style="background-color:#2692D0;color:white;">
                                    <tr>
                                        <th style="white-space:nowrap;padding-right: 30px">Shift</th>
                                        <th style="white-space:nowrap;padding-right: 80px">Date</th>
                                        <th style="white-space:nowrap;padding-right: 30px">Time In-Out</th>
                                        <th style="white-space:nowrap;padding-right: 30px">Assigned By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
        </div>


        </form>
    </div>
</div>



