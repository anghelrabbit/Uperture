<div class="modal modal-primary fade" name='modal_account'>
    <div class="modal-dialog ">
        <div class="modal-content">
            <!-- Widget: user widget style 1 -->
                <div class="box box-widget widget-user">
                    <button type="button" class="close pull-right"  data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header" style="background-color: #2692D0;color:white">
                        <h3 class="widget-user-username" name="profile_name"></h3>
                        <h5 class="widget-user-desc" name="profile_dept">Department</h5>
                    </div>
                    <div class="widget-user-image">
                        <img style="height:100px" class="img-circle" src="assets/images/profile.png" alt="User Avatar" name="image_profile">
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-sm-6 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">Username</h5>
                                    <span class="description-text" name="profile_username"></span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-6 ">
                                <div class="description-block">
                                    <h5 class="description-header">Password</h5>
                                    <span class="description-text" name="profilen_password"></span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                           
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                     <div class="overlay" name="lock_overlay">
                             <button type="button" class="close pull-right"  data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                   <div class=" lockscreen hold-transition " style="background-color: transparent ">
                    <div class="lockscreen-wrapper" >
                        <div class="lockscreen-logo">
                            <!--<a href="" style="font-size: 25px">Solea Hotel Cebu Corporation</a>-->

                        </div>
                        <div class="lockscreen-name text-center" style="margin-top:20%">
                            <label style="font-size: 10px;color:#D2D6DE">.</label>
                        </div>

                        <div class="lockscreen-item" style="margin-top:5%;" >
                            <div class="lockscreen-image" style="border:solid;border-color: #2692D0;">
                                <img src="assets/images/Lock-icon.png" alt="User Image">
                            </div>

                            <div class="lockscreen-credentials has-danger" >
                                <div class="input-group" style="border:solid;border-color: #2692D0">
                                    <input type="password" name="profile_password" class="form-control " placeholder="password" style="margin-left:-1px"  onkeypress="profile_account_keypress(event)" onkeyup="enable_enterkey()">
                                    <div class="input-group-btn" >
                                        <span type="button" class="btn "   ><i class="fa fa-arrow-right text-muted" onclick="check_profile_account()"></i></span>
                                    </div>
                                </div>
                            </div>

                        </div>
                       
                    </div>
                </div>
                    
                </div>
                </div>
            
            <!-- /.widget-user -->


        </div>
    </div>
</div>