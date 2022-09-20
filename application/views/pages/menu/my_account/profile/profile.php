  

<section class="content">

    <div class="row">
        <div class="col-lg-3">


            <!-- Profile Image -->
            <div class="box ">
                <div class="box box-widget widget-user-2">
                    <div class="widget-user-header bg-primary" style="background-color:#2692D0">
                        <div class="widget-user-image">
                            <img style="height:70px" class="img-circle" src="data:image;base64,<?php echo $profile_image ?>" alt="User Avatar">
                        </div>
                        <h3 class="widget-user-username"><?= $employeeprofile[0]->lastname . ", " . $employeeprofile[0]->firstname ?></h3>
                        <h5 class="widget-user-desc">
                            <?php
//                            $counter = 0;
//                            if (strlen($jobposition) > 23) {
//                                foreach ($jobposition as $new_job_position) {
//                                    if ($counter == 2) {
//                                        echo $new_job_position . ' ';
//                                        echo '&nbsp;';
//                                        $counter = 0;
//                                    } else {
//                                        echo $new_job_position . ' ';
//                                        $counter++;
//                                    }
//                                }
//                            } else {
                                echo $jobposition;
//                            }
                            ?>
                        </h5>
                    </div>
                    <div class="box-footer no-padding">
                        <ul class="nav nav-stacked" onmouseout="showWifi(false)">
                            <li><a href="#">Employee No<span class="pull-right"><?= $employeeprofile[0]->empid ?></span></a></li>
                            <li><a href="#">Job Status<span class="pull-right"><?= $employeeprofile[0]->empstatus ?></span></a></li>
                            <li><a href="#">Date Hired<span class="pull-right"><?= date('F d, Y', strtotime($employeeprofile[0]->datehired)) ?></span></a></li>
                            <li><a href="#">Department<span class="pull-right"> Marketing</span></a></li>
                            
                        </ul>
                    </div>
                </div>
            </div>
            <?php if ($this->session->userdata('profileno') == $employeeprofile[0]->profileno) { ?>
                <div class="box bg-gray color-palette box-primary" style="border-top-color:#2692D0">
                    <div class="box-header with-border " >
                        <h3 class="box-title " style="font-weight:bold">Account Settings</h3>
                    </div>
                    <div class="box-body">
                        <form class="form-horizontal" >
                            <div class="form-group ">
                                <label class="col-sm-8 ">Username:</label>
                                <div class="col-sm-12">
                                    <div class="input-group col-sm-12 ">
                                        <input type="text" class="form-control " id="profile_username" value="<?php echo $employeeprofile[0]->username ?>"  readonly/>

                                    </div>
                                </div>
                            </div>

                            <div class="form-group ">
                                <label class="col-sm-8 ">New Password:</label>
                                <div class="col-sm-12">
                                    <input type="password" class="form-control"  name="new_password"/>
                                    <label class="hidden" style="color:red" name="new_password_error">New Password is required.</label>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label class="col-sm-8 ">Confirm Password:</label>
                                <div class="col-sm-12">
                                    <input type="password" class="form-control" name="confirm_password"/>
                                    <label class="hidden" style="color:red" name="confirm_new_password_error">Password did not match</label>
                                </div>
                            </div>


                        </form>
                        <button type="button" class="btn " style="float:right; background-color:#3ED03E;color:white" onclick="changeAccount()">Update Changes</button>
                    </div>
                    <div class="overlay" name="lock_overlay">
                        <div class=" lockscreen hold-transition " style="background-color: transparent ">
                            <div class="lockscreen-wrapper" >
                                <div class="lockscreen-logo">
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
                                <div class=" text-center" style="color:#D2D6DE">
                                    .
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="col-md-5 col-lg-5">
            <div class="box box-solid ">
                <div class="box-header with-border ">
                    <ul class="nav nav-tabs">
                        <li   class="active" onclick="tab_selection(0)" ><a   name="personal_tab" href="#personalinfo" data-toggle="tab" style="font-weight: bold;">Personal</a></li>
                        <li   class="" onclick="tab_selection(1)" ><a   name="contact_tab" href="#contactinfo" data-toggle="tab" style="font-weight: bold;">Contacts</a></li>
                        <li   class="" onclick="tab_selection(2)" ><a   name="contribution_tab" href="#contributioninfo" data-toggle="tab" style="font-weight: bold;">Contribution</a></li>
                        <li   class="" onclick="tab_selection(3)" ><a   name="integration_tab" href="#integrationinfo" data-toggle="tab" style="font-weight: bold;">Salary Integrations</a></li>
                         <?php if (($this->session->userdata('profileno') == $employeeprofile[0]->profileno && $this->session->userdata('oncall') == 0) || $this->session->userdata('hr') == 1) { ?>
                            <!--<li   onclick="payslip_table();tab_selection(1)" ><a name="payslip_tab" href="#worksched" data-toggle="tab" style="font-weight: bold;"> Payslip  </a> </li>-->
                        <?php } ?>
                        <li class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                        </li>
                    </ul>   

                </div>
                <div class="box-body">
                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="active tab-pane" id="personalinfo">
                                <?php $this->load->view('pages/menu/my_account/profile/tabs/personal_information'); ?>
                            </div>
                            <div class="tab-pane" id="contactinfo">
                                <?php $this->load->view('pages/menu/my_account/profile/tabs/contact_information'); ?>
                            </div>
                            <div class="tab-pane" id="contributioninfo">
                                <?php $this->load->view('pages/menu/my_account/profile/tabs/contribution_information'); ?>
                            </div>
                            <div class="tab-pane" id="integrationinfo">
                                <?php $this->load->view('pages/menu/my_account/profile/tabs/integration_information'); ?>
                            </div>

                            <div class="tab-pane" id="worksched">
                                <?php $this->load->view('pages/menu/my_account/profile/tabs/payslip'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php $this->load->view('pages/menu/my_account/profile/tabs/workschedule'); ?>
    </div>
</section>
<?php // $this->load->view('pages/modal/my_account_modal/change_password/change_password_modal'); ?>      




<script type="text/javascript">
<?php if ($this->session->userdata('profileno') == $employeeprofile[0]->profileno) { ?>
        document.addEventListener('DOMContentLoaded', function () {
            $(".sidebar-menu").find(".active").removeClass("active");
            $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
            $('#myaccountmenu').addClass('active');
            $('#myInformationHolder').addClass('active');
            $('#infocircle').addClass('text-aqua');
            $('#myaccountmenu').addClass('menu-open');
        });
<?php } else { ?>
        document.addEventListener('DOMContentLoaded', function () {
            $(".sidebar-menu").find(".active").removeClass("active");
            $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
            $('#employeesmenu').addClass('active');
            $('#employee201').addClass('active');
            $('#employee201_circle').addClass('text-aqua');
            $('#employeesmenu').addClass('menu-open');
        });
<?php } ?>
</script>
