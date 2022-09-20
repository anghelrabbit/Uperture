
<form class="form-horizontal">

    <div class="row">
        <div class="col-md-6 ">
            <div class="form-group ">
                <span class="col-md-4 col-sm-2 control-label">First Name:</span>
                <div class="col-md-8 col-sm-10">
                    <input type="text" value="" class="form-control" >
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group ">
                <span class="col-md-3 col-sm-2 control-label">Last Name:</span>
                <div class="col-md-9 col-sm-10">
                     <input type="text" value="" class="form-control" >

                    </select>
                </div>
            </div>
        </div>
    </div>
    
     <div class="row">
        <div class="col-md-6 ">
            <div class="form-group ">
                <span class="col-md-4 col-sm-2 control-label">Middle Name:</span>
                <div class="col-md-8 col-sm-10">
                    <input type="text" value="" class="form-control" >
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group ">
                <span class="col-md-3 col-sm-2 control-label">Location:</span>
                <div class="col-md-9 col-sm-10">
                     <input type="text" value="" class="form-control" >

                    </select>
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="row">
        <div class="col-md-6 ">
            <div class="form-group ">
                <span class="col-md-4 col-sm-2 control-label">Birthdate:</span>
                <div class="col-md-8 col-sm-10">
                    <input type="date" class="form-control" value="<?= $employeeprofile[0]->birthdate ?>" />
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group ">
                <span class="col-md-3 col-sm-2 control-label">Gender:</span>
                <div class="col-md-9 col-sm-10">
                    <input type="text" value="<?php echo $employeeprofile[0]->sex ?>" class="form-control" >

                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group ">
                <span class="col-md-4 col-sm-2 control-label">Civil Status:</span>
                <div class="col-md-8 col-sm-10">
                    <input type="text" class="form-control" value="<?php echo $employeeprofile[0]->civilstatus ?>" >

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <span class="col-md-3 col-sm-2 control-label">Nationality:</span>
                <div class="col-md-9 col-sm-10"> 
                    <input type="text" class="form-control" value="<?= $employeeprofile[0]->nationality ?>" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group ">
                <span class="col-md-4 col-sm-2 control-label">Religion:</span>
                <div class="col-md-8 col-sm-10">
                    <input type="text" class="form-control" value="" >

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <span class="col-md-3 col-sm-2 control-label">Postal Address</span>
                <div class="col-md-9 col-sm-10"> 
                    <input type="text" class="form-control" value="" />
                </div>
            </div>
        </div>
    </div>
    
</form>

