
<section class="content" >
    <?php // $this->load->view('templates/structure') ?>
    <br>

    <div class="box box-primary" name="dashboard_box">

        <div class="box-header with-border">
            <div class="row">



                <div class="col-md-3 col-lg-3 col-xs-6">
                    <div class="form-group " id="" style="color:black">
                        <span class="" style="letter-spacing: 0.5px">From</span>
                        <input type="date" class="form-control" id="worksched_in" onchange="setupPayPeriod()" onkeyup="$('#datefiled_in').val('')" value="<?php echo date('Y-m-d') ?>">
                    </div>

                </div>
                <div class="col-md-3 col-lg-3 col-xs-6">
                    <div class="form-group " id="" style="color:black">
                        <span class="" style="letter-spacing: 0.5px">To</span>
                        <input type="date" class="form-control" id="worksched_in" onchange="setupPayPeriod()" onkeyup="$('#datefiled_in').val('')" value="<?php echo date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 col-xs-6">
                    <div class="form-group " id="" style="color:black">
                        <span class="" style="letter-spacing: 0.5px">Department</span>
                        <select class="form-control form-control-sm" id="request_division" name="" onchange="tabCategory('');">

                            <option value="All">All</option>
                            <option value="DIV-003">Business Development</option>
                            <option value="DIV-004">Home Ambassadors</option>
                            <option value="DIV-005">Book That Condo</option>
                            <option value="DIV-006">Royalty Cleaning</option>
                            <option value="DIV-007">Customer Service</option>
                            <option value="DIV-009">Night Calls</option>
                            <option value="DIV-010">Owner Experience</option>
                            <option value="DIV-011">Accounting</option>
                            <option value="DIV-008">Tahoe Truckee</option>
                            <option value="DIV-012">Training Development</option>
                        </select>    

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
                                    <a href="#pending_bonus" data-toggle="tab"  onclick="tabCategory(0)">Pending Bonuses&nbsp;&nbsp;</a>
                                </li>
                                <li class="" >
                                    <a href="#approved_bonuses" data-toggle="tab"  onclick="tabCategory(0)">Approved  Bonuses&nbsp;&nbsp;</a>
                                </li>
                                <li class="" >
                                    <a href="#denied_bonuses" data-toggle="tab"  onclick="tabCategory(0)">Denied  Bonuses&nbsp;&nbsp;</a>
                                </li>


                            </ul>   
                            <div class="tab-content">
                                <div class="active tab-pane" id="pending_bonus" >
                                    <?php $this->load->view('pages/menu/bonus/tabs/pending_bonus'); ?>
                                </div>
                                <div class="tab-pane" id="approved_bonuses" >
                                    <?php $this->load->view('pages/menu/bonus/tabs/approved_bonus'); ?>
                                </div>
                                <div class="tab-pane" id="denied_bonuses" >
                                    <?php $this->load->view('pages/menu/bonus/tabs/denied_bonuses'); ?>
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
        $('#bonuses_menu').addClass('active');

    });
</script>
