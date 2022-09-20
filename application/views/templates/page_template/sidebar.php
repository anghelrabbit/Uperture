<aside class="main-sidebar">
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <img style="height:50px" src="data:image;base64,<?php echo $this->session->userdata('profilepic'); ?>" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p class=""  ><?= $this->session->userdata('empname') ?></p>
                <a class="designation" ><?php
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
                    ?></a>
            </div>
        </div>
        <ul class="sidebar-menu" data-widget="tree">
            <li id="dashboardmenu">
                <a href="Homepage" >
                    <i class="ion ion-android-home"></i> <span>Dashboard</span><span name="dashboard_alert" class="badge pull-right hidden" style="background-color:#FFB347">!</span>
                </a>
            </li>
            <?php if ($this->session->userdata('head') == 1) { ?>
                <li id="teamWorkSchedule">
                    <a href="ScheduleManagement" >

                        <i class="ion ion-calendar"></i> <span>Team Work Schedule</span>
                    </a>
                </li>

                <li id="teamTimeLogs">
                    <a href="TeamTimeLogs" >

                        <i class="ion ion-clock"></i> <span>Team Time Logs</span>
                    </a>
                </li>
            <?php } ?>



            <?php if ($this->session->userdata('payroll') == 1) { ?>
                <li id="payroll_menu">
                    <a href="Payroll" >
                        <i class="ion ion-android-folder-open"></i> <span>Payroll</span>
                    </a>
                </li>
                <li id="last_payments_menu">
                    <a href="LastPayments" >
                        <i class="ion ion-android-archive"></i> <span>Payment History</span>
                    </a>
                </li>
                <li id="reimbursement_menu">
                    <a href="Reimbursements" >
                        <i class="ion ion-refresh"></i> <span>Reimbursements</span><span name="announcement_alert" class="badge pull-right hidden" style="background-color:#FFB347">!</span>
                    </a>
                </li>
                <li id="bonuses_menu">
                    <a href="Bonus" >
                        <i class="ion ion-star"></i> <span>Bonus</span><span name="announcement_alert" class="badge pull-right hidden" style="background-color:#FFB347">!</span>
                    </a>
                </li>
                <li id="overtimelogs_menu">
                    <a href="OvertimeLogs" >
                        <i class="ion ion-clock"></i> <span>Overtime Logs</span><span name="announcement_alert" class="badge pull-right hidden" style="background-color:#FFB347">!</span>
                    </a>
                </li>

            <?php } ?>

            <?php if ($this->session->userdata('hr') == 1 || $this->session->userdata('payroll') == 1) { ?>
                <li id="employee201">
                    <a href="Emp201File" >
                        <i class="ion ion-android-people"></i> <span>201 File</span>
                    </a>
                </li>


            <?php } ?>


            <?php if ($this->session->userdata('hr') == 1 || $this->session->userdata('head') == 1 || $this->session->userdata('scheduler') == 1 || $this->session->userdata('payroll') == 1) { ?>
                <li id="employeeAttendance_menu">
                    <a href="employeeAttendance" >
                        <i class="ion ion-person"></i> <span>Employee Attendance</span>
                    </a>
                </li>
                <li id="pendingformsmenu">
                    <a href="PendingForms" >

                        <i class="ion ion-clipboard"></i> <span>Pending Leave Forms</span><span class="badge pull-right hidden" name="pending_forms_alert" style="background-color:#FFB347">!</span>
                    </a>
                </li>

            <?php } ?>



            <?php if ($this->session->userdata('hr') == 1) { ?>
                <li id="accountapprovalmenu">
                    <a href="AccountApproval" >

                        <i class="ion ion-person-add"></i> <span>Account Approval</span><span class="badge pull-right hidden" name="" style="background-color:#FFB347">!</span>
                    </a>
                </li>

            <?php } ?>




            <?php if ($this->session->userdata('user') == 0) { ?>
                <li id="announcementmenu">
                    <a href="Announcement" >
                        <i class="ion ion-android-notifications"></i> <span>Announcement</span><span name="announcement_alert" class="badge pull-right hidden" style="background-color:#FFB347">!</span>
                    </a>
                </li>

            <?php } ?>






            <!--            <li id="relievermenu">
                            <a href="Reliever" >
                                <i class="fas fa-handshake"></i> <span>&nbsp;Approve as Reliever</span><span name="reliever_alert" class="badge pull-right hidden" style="background-color:#FFB347">!</span>
                            </a>
                        </li>-->
            <?php if ($this->session->userdata('user') == 0 && $this->session->userdata('hr') == 1) { ?>
                <li id="leavecreditsmenu">
                    <a href="LeaveCredits" >
                        <i class="ion ion-android-list"></i> <span>Leave Credits</span>
                    </a>
                </li>
            <?php } ?>


            <?php if ($this->session->userdata('payroll') != 1) { ?>
                <li class="treeview" id="myaccountmenu">
                    <a href="#">
                        <i class="fa fa-user" ></i>
                        <span>My Account</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu" >
                        <li id="myInformationHolder"><a href="Profile"><i class="far fa-circle" id="infocircle"></i>&nbsp;&nbsp;Profile</a></li>
                        <li id="DTRHolder"><a href="DailyTimeRecord"><i class="far fa-circle" id="dtrcircle"></i>&nbsp;&nbsp;Daily Time Record</a></li>
                        <li id="RequestFormHolder"><a href="RequestForms"><i class="far fa-circle" id="requestformcircle"></i>&nbsp;&nbsp;Request Forms</a></li>
                        <li id="RequestOvertimeHolder"><a href="RequestOvertime"><i class="far fa-circle" id="requestovertimecircle"></i>&nbsp;&nbsp;Request Overtime</a></li>
                        <li id="RequestReimbursementHolder"><a href="RequestReimbursement"><i class="far fa-circle" id="requestreimbursementcircle"></i>&nbsp;&nbsp;Request Reimbursement</a></li>
                    </ul>
                </li>
            <?php } ?>


            <?php if ($this->session->userdata('user') == 0) { ?>
                <!--                <li class="treeview" id="employeesmenu">
                                    <a  class="menu-toggle">
                                        <i class="fa fa-archive"></i>
                                        <span>Reports</span>
                                        <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li id="employee201"><a href="Emp201File"><i class="far fa-circle" id="employee201_circle"></i>&nbsp;&nbsp;201 File</a></li>
                                        <li id="emp_workschedule"><a href="WorkSchedule"><i class="far fa-circle" id="emp_worksched"></i>&nbsp;&nbsp;Work Schedule</a></li>
                                        <li id="request_form_reports"><a href="ApprovedForms" ><i class="far fa-circle" id="form_report_circle"></i>&nbsp;&nbsp;Approved Forms</a></li>
                <?php if ($this->session->userdata('payroll') == 1) { ?>
                                                                        <li id="bank_transmittal"><a href="BankTransmittal" ><i class="far fa-circle" id="bank_transmittal_circle"></i>&nbsp;&nbsp;Payroll</a></li>
                <?php } ?>
                                    </ul>
                                </li>-->
            <?php } ?>

            <?php if ($this->session->userdata('user') == 0 && $this->session->userdata('hr') == 1) { ?>
                <li id="dtrmenu">
                    <a href="DTRLog" >
                        <!--<i class="glyphicon glyphicon-book"></i> <span>DTR Log</span>-->
                    </a>
                </li>
            <?php } ?>


        </ul>
    </section>
</aside>