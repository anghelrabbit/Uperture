<header class="main-header " >
    <!-- Logo -->
    <a href="../../index2.html" class="logo" id="nav_little">
        <span class="logo-mini"><img height="30" width="30"  src="data:image;base64,<?php echo $this->session->userdata('complogo'); ?>"  alt="User Image"></span>
        <span class="logo-lg" style="font-size: 14px"><?php echo $this->session->userdata('compname') ?></span>
    </a>


    <nav class="navbar navbar-static-top" style="background-color:#2692D0">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        </a>
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                <i class="fa fa-clock-o"></i>
            </button>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse pull-left" id="navbar-collapse" >

                <form class="navbar-form navbar-left" role="search" >
<!--                    <div class="form-group">
                        <div class="input-group " id="" style="color:black" >
                            <div class=" input-group-addon ">
                                <i class="">Punch-in</i>
                            </div>
                            <input  class="form-control " id="current_timein" readonly > 
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group " id="" style="color:black">
                            <div class="input-group-addon">
                                <i class="">Punch-out</i>
                            </div>
                            <input  class="form-control success" id="current_timeout" readonly>
                        </div>
                    </div>-->
                    <div class="form-group">
<!--                        <span class="btn" onclick="refreshCurrentDTR()" style="background-color:#3ED03E;color:white"> <i class="fa fa-refresh" id="refresh_icon" style="font-size:18px"></i></span>
                        <a class="btn" href="DailyTimeRecord" style="background-color:#3ED03E;color:white"><i class="fa fa-fingerprint " style="font-size:18px"></i></a>-->
                        
                        <!--<a class="btn" href="DailyTimeRecord" style="background-color:#3ED03E;color:white"><i class="fa fa-check " style="font-size:18px"></i> On Duty</a>-->
                        <a class="btn" href="#" onclick="empPunch(1)" style="background-color:#3ED03E;color:white"><i class="fa fa-clock " style="font-size:18px"></i> Punch In</a>
                        <a class="btn" href="#" onclick="empPunch(0)" style="background-color:#FF392E;color:white"><i class="fa fa-clock " style="font-size:18px"></i> Punch Out</a>
                       
                        <!--<a class="btn" href="RequestOvertime" style="background-color:#3ED03E;color:white"><i class="fa fa-clock " style="font-size:18px"></i> Overtime</a>-->
                        
<!--                        <button type="button" class="btn " style="float:right; background-color:#3ED03E;color:white" onclick="">On Duty</button>
                        <button type="button" class="btn " style="float:right; background-color:#3ED03E;color:white" onclick="" href="RequestOvertime">Overtime</button>-->
                        
                        
                    </div>
                </form>
            </div>
        <!-- /.navbar-collapse -->
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
                <!--                <li class="dropdown notifications-menu" >
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="notification_dropdown">
                                        <i class="fa fa-bell-o " id="bell_ring"></i>
                                        <span class="label label-warning hidden" id="notification_warning">!</span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class="header" onclick='goToNotifPage("hrheaddashboard/0")'>Announcement <span class="label label-success  pull-right" id="new_announce_notif">0</span></li>
                                        <li class="header" onclick='goToNotifPage("employeeevaluation")'>Evaluation <span class="label label-success  pull-right" id="new_eval_notif">0</span></a></li>
                                        <li class="header" onclick='gotoAnnouncementPage()'>Notice To Explain <span class="label label-success  pull-right" id="new_nte_notif">0</span></a></li>
                                    </ul>
                                </li>-->
                <?php if ($this->session->userdata('profileno') == '07302019105651562PWD') { ?>
                    <li class="dropdown notifications-menu"  onclick="openSettingModal()" >

                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="">
                            <span><i class="fa fa-cog fa-spin "></i></span>
                        </a>

                    </li>
                <?php } ?>
                <li class="dropdown notifications-menu" >

                    <a class="btn" href="#" name="take_break" style="background-color:#3ED03E;color:white;font-size:15px;letter-spacing:0.5px"><i class="fa fa-clock " style="font-size:18px"></i> <span name="span_timer">Break</span></a>

                </li>
                <li class="dropdown notifications-menu" onclick="idleAccount()">

                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="">
                        <i class="fa fa-lock" ></i>
                    </a>

                </li>

                <li class="dropdown user user-menu" >
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!--<img src="<?= $this->session->userdata('imgportpath') ?>" class="user-image" alt="User Image">-->
                        <img  src="data:image;base64,<?php echo $this->session->userdata('profilepic'); ?>" class="user-image" alt="User Image">
                        <!--<img src="assets/images/profile.png" class="user-image" alt="User Image">-->
                        <span class="hidden-xs"><?= $this->session->userdata('empname') ?></span>
                    </a>
                    <ul class="dropdown-menu " >

                        <li class="user-header modal-backdrop-orange" >
                            <!--<img src="<?= $this->session->userdata('imgportpath') ?>" class="img-circle" alt="User Image">-->
                            <img src="data:image;base64,<?php echo $this->session->userdata('profilepic'); ?>" class="img-circle" alt="User Image">

                            <p>
                                <?= $this->session->userdata('empname') ?>
                                <small>
                                    <?php
                                    $job_pos = explode(' ', $this->session->userdata('jobposition'));
                                    $counter = 0;
                                    if (strlen($this->session->userdata('jobposition')) > 23) {
                                        foreach ($job_pos as $new_job_position) {
                                            if ($counter == 2) {
                                                echo $new_job_position . ' ';
                                                echo '<br>';
                                                $counter = 0;
                                            } else {
                                                echo $new_job_position . ' ';
                                                $counter++;
                                            }
                                        }
                                    } else {
                                        echo $this->session->userdata('jobposition');
                                    }
                                    ?>
                                </small>
                            </p>
                        </li>

                        <li class="user-footer " style="background-color: #222D32" >
                            <div class="pull-left">
                                <a href="Profile" class="btn btn-default btn-flat ">Profile</a>
                            </div>
                            <div class="pull-right">
                                <a href="Signout" class="btn btn-default btn-flat">Sign out</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>


        </div>
        <!-- /.navbar-custom-menu -->
        <!-- /.container-fluid -->
    </nav>
</header>