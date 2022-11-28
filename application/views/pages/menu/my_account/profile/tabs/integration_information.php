
<form class="form-horizontal">

    <div class="row">
        <div class="col-md-6 ">
            <div class="form-group ">
                <span class="col-md-4 col-sm-2 control-label">Pay Period:</span>
                <div class="col-md-8 col-sm-10">
                    <input type="text" value="" class="form-control" readonly>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group ">
                <span class="col-md-3 col-sm-2 control-label">Integration:</span>
                <div class="col-md-9 col-sm-10">

                    <select name="payment_integration" id="payment_integration" class="form-control">
                        <option  value="1">Paypal</option>
                        <option value="2">Wise</option>
                        <option value="3">Bank</option>
                        <!--<option value="0.5">Half Day</option>-->
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    
    <!-- Paypal User -->
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group ">
                <span class="col-md-4 col-sm-2 control-label">Email Address:</span>
                <div class="col-md-8 col-sm-10">
                    <input type="text" class="form-control" value="" >

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <span class="col-md-3 col-sm-2 control-label">Account Name: </span>
                <div class="col-md-9 col-sm-10"> 
                    <input type="text" class="form-control" value="" />
                </div>
            </div>
        </div>
    </div>
    
    
     <!-- Bank User -->
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group ">
                <span class="col-md-4 col-sm-2 control-label">Bank:</span>
                <div class="col-md-8 col-sm-10">
                    <input type="text" class="form-control" value="" >

                </div>
            </div>
        </div>
        
    </div>
     
     <div class="row">
        <div class="col-md-6">
            <div class="form-group ">
                <span class="col-md-4 col-sm-2 control-label">Account No.:</span>
                <div class="col-md-8 col-sm-10">
                    <input type="text" class="form-control" value="" >

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <span class="col-md-3 col-sm-2 control-label">Swift code: </span>
                <div class="col-md-9 col-sm-10"> 
                    <input type="text" class="form-control" value="" />
                </div>
            </div>
        </div>
    </div>
    
    
    
</form>
