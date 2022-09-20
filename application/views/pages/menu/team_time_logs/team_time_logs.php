
<section class="content" >
   <?php // $this->load->view('pages/menu/dashboard/dashboard_templates/employees_total'); ?>
    <br>

    <div class="box box-primary" name="dashboard_box">

       
        <div class="box-body no-padding">
            <div class="row">

                <div class="col-lg-12 col-xs-12 col-md-12">
                    <div class="pad">
                        <div class="nav-tabs-custom " id="table_tabs">
                            <ul class="nav nav-tabs ">
                                <li >

                                </li>
                                <li class="active" >
                                    <a href="#teamTimeLogsToday" data-toggle="tab"  onclick="tabCategory(0)">Today&nbsp;&nbsp;</a>
                                </li>
                                <li class="" >
                                    <a href="#teamtimelogs" data-toggle="tab"  onclick="tabCategory(1)">Time Logs&nbsp;&nbsp;</a>
                                </li>
                                 <li class="" >
                                    <a href="#teamTimeOvertime" data-toggle="tab"  onclick="tabCategory(2)">Overtime&nbsp;&nbsp;</a>
                                </li>
<!--                                <li class="" >
                                    <a href="#teamTimeUndertime" data-toggle="tab"  onclick="tabCategory(0)">Undertime&nbsp;&nbsp;</a>
                                </li>-->
                                


                            </ul>   
                            <div class="tab-content">
                                <div class="active tab-pane" id="teamTimeLogsToday" >
                                    <?php $this->load->view('pages/menu/team_time_logs/tabs/time_logs_today'); ?>
                                </div>
                                <div class="tab-pane" id="teamtimelogs" >
                                    <?php $this->load->view('pages/menu/team_time_logs/tabs/time_logs'); ?>
                                </div>
                                <div class="tab-pane" id="teamTimeOvertime" >
                                    <?php $this->load->view('pages/menu/team_time_logs/tabs/team_overtime'); ?>
                                </div>
                                
                               
                                


                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>


<?php $this->load->view('pages/modal/modal_leave/modal_leave'); ?>
<?php $this->load->view('pages/modal/modal_undertime/modal_undertime'); ?>
<?php $this->load->view('pages/modal/modal_cs/modal_cs'); ?>
<?php $this->load->view('pages/modal/modal_overtime/modal_overtime'); ?>

<script id="signatory-template" type="text/x-custom-template">
    <?php $this->load->view('templates/signatories') ?>
</script>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        $(".sidebar-menu").find(".active").removeClass("active");
        $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
        $(".sidebar-menu").find(".menu-open").removeClass("menu-open");
        $('#teamTimeLogs').addClass('active');

    });
</script>


<!-- <nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">WebSiteName</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
       <li class="active"><a href="#pane1" data-toggle="tab">ELECTRONIC<br>APPLE TREES</a></li>
            <li><a href="#pane2" data-toggle="tab">CROSSING GUARD<br>ORANGE TREES</a></li>
            <li><a href="#pane3" data-toggle="tab">POLICE BODY<br>PEARS TREES</a></li>
            <li><a href="#pane4" data-toggle="tab">PARKING METERS<br>&nbsp;</a></li>
            <li><a href="#pane4" data-toggle="tab">TRANSPORTATION<br>GRAPES TREES</a></li> 
      </ul>

    </div>
  </div>
</nav>-->