<!-- Large Size -->

<div  class="modal modal-primary fade" name="add_holiday" tabindex="-1" role="dialog"  aria-hidden="true" >
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Holiday</h4>
            </div>


            <div class="modal-body">
                <form id="holiday_form" class="form-horizontal">
                    <div class="form-group">
                        <div class="col-lg-4">
                            <label>Date</label>
                            <input type="date" class="form-control"  name="holiday_date" placeholder="Date" style="border:solid lightgrey" >
                            <label name="holiday_date_error" style="color:red;"></label>
                        </div>
                        
                        <div class="col-lg-4">
                            <label>Holiday</label>
                            <input type="text" class="form-control"  name="holiday_name" placeholder="Holiday" style="border:solid lightgrey">
                            <label name="holiday_name_error" style="color:red;"></label>
                        </div>
                        <div class="col-lg-4">
                            <label>Type</label>
                            <select class="form-control show-tick" name="holiday_type"> 
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
 <button class="btn btn-success pull-right" style="background-color: #3ED03E; color:white" name="save_holiday" >Save</button>
               
            </div>


        </div>
    </div>
</div>

