
<section class="content" >
    <?php // $this->load->view('templates/structure') ?>
    <br>

    <div class="box box-primary" name="dashboard_box">

        <div class="box-header with-border">
            <div class="row">
                <div class="col-lg-2 col-xs-6 col-md-3">
                    <div class="form-group">
                        <span class="" style="letter-spacing: 0.5px">Category</span>
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" name="category_button">
                                <span class="badge hidden" style="background-color:#FFB347;color:white" name="category_alert">!</span>
                               &nbsp;&nbsp; Filed Forms
                                &nbsp;<span class="fa fa-caret-down" ></span></button>
                            <ul class="dropdown-menu">
                                <li name="filed_forms"><a href="#">Filed Forms <span class="badge pull-right" name="pending_forms_count" style="background-color:#FFB347;color:white"></span></a> </li>
                                <li class="divider"></li>
                                <li name="filed_cancel"><a href="#">Cancellation  <span class="badge pull-right" name="cancel_forms_count"  style="background-color:#FFB347;color:white">!</span></a> </li>
                            </ul>

                        </div>
                    </div>
                </div>

                <div class="col-lg-1 col-md-2 col-sm-2  col-xs-6">
                    <div class="form-group">
                        <span style="letter-spacing:0.5px">By Cut-off</span><br>
                        <input name="cut_off_toggle" checked type="checkbox" onchange="" >
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 col-xs-6">
                    <div class="form-group " id="" style="color:black">
                        <span class="" style="letter-spacing: 0.5px">Forms Filed From</span>
                        <input type="date" class="form-control" id="worksched_in" onchange="setupPayPeriod()" onkeyup="$('#datefiled_in').val('')" value="<?php echo date('Y-m-d') ?>">
                    </div>

                </div>
                <div class="col-md-3 col-lg-3 col-xs-6">
                    <div class="form-group" id="" style="color:black">
                        <span class="" style="letter-spacing: 0.5px">Forms Filed To</span>
                        <input type="date" class="form-control"   id="worksched_out"  readonly>
                    </div>
                </div>

            </div>





        </div>
        <div class="box-body no-padding">
            <div class="row">

                <div class="col-lg-12 col-xs-12 col-md-12">
                    <div class="pad">
                        <div class="nav-tabs-custom " id="table_tabs">
                            <ul class="nav nav-tabs ">
                                <li >

                                </li>
                                <li class="active" >
                                    <a href="#pendingleave" data-toggle="tab"  onclick="tabCategory(0)">Leave&nbsp;&nbsp;<span class="badge pull-right" name="leave_alert" style="background-color:#FFB347;letter-spacing:1px"></span></a>
                                </li>
<!--                                <li >
                                    <a href="#pendingundertime" data-toggle="tab" onclick = "tabCategory(1)">Undertime &nbsp;&nbsp;<span class="badge pull-right" name="undertime_alert" style="background-color:#FFB347;letter-spacing:1px"></span></a>
                                </li>
                                <li >
                                    <a href="#pendingchangesched" data-toggle="tab"  onclick="tabCategory(2);">Change Schedule&nbsp;&nbsp;<span class="badge pull-right" name="cs_alert" style="background-color:#FFB347;letter-spacing:1px"></span> </a>
                                </li>
                                <li >
                                    <a href="#pendingovertime" data-toggle="tab"  onclick="tabCategory(3);">Overtime&nbsp;&nbsp;<span class="badge pull-right" name="overtime_alert" style="background-color:#FFB347;letter-spacing:1px"></span> </a>
                                </li>-->


                            </ul>   
                            <div class="tab-content">
                                <div class="active tab-pane" id="pendingleave" >
                                    <?php $this->load->view('pages/menu/pendingforms/tabs/leave'); ?>
                                </div>
                                <div class="tab-pane" id="pendingundertime" >
                                    <?php $this->load->view('pages/menu/pendingforms/tabs/undertime'); ?>
                                </div>
                                <div class="tab-pane" id="pendingchangesched" >
                                    <?php $this->load->view('pages/menu/pendingforms/tabs/change_schedule'); ?>
                                </div>
                                <div class="tab-pane" id="pendingovertime" >
                                    <?php $this->load->view('pages/menu/pendingforms/tabs/overtime'); ?>

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
        $('#pendingformsmenu').addClass('active');

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