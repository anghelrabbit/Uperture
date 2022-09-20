<!-- Large Size -->

<div  class="modal modal-primary fade" name="modal_merge_holiday" tabindex="-1" role="dialog"  aria-hidden="true" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Copy Holidays</h4>
            </div>


            <div class="modal-body">
                <form id="holiday_form" class="form-horizontal">
                    <div class="form-group">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <label style="font-size:13px;letter-spacing:1px">Copy Holidays from</label>
                            <input type="number" class="form-control"  name="year_based" placeholder="Date" style="border:solid lightgrey"  value="<?php echo date('Y', strtotime(date('Y-m-d') . '-1 year')) ?>">
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <label style="font-size:13px;letter-spacing:1px">Paste Holidays To</label>
                            <input type="number" class="form-control"  name="year_selected" placeholder="Holiday" style="border:solid lightgrey" value="<?php echo date('Y', strtotime(date('Y-m-d'))) ?>">
                        </div>

                    </div>
                </form>
                <div class="alert" style="letter-spacing:1px;background-color:#FF392E">
                    <span style="font-size:15px;"> Note:  Removes holiday entries of <label name="year_pasted" style="text-decoration: underline;font-size:16px"></label> 
                        and copy holidays of
                        <label name="year_copied" style="text-decoration: underline;font-size:16px"></label></span>

                </div>  
            </div>
            <div class="modal-footer">
                <button class="btn btn-success pull-right" style="background-color: #3ED03E; color:white" name="merge_holiday" >Merge</button>

            </div>


        </div>
    </div>
</div>

