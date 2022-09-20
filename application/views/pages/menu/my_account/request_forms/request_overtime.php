<style>
    .my-box-icon {
        border-top-left-radius: 2px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 2px;
        display: block;
        float: left;
        height: 90px;
        width: 90px;
        text-align: center;
        font-size: 45px;
        line-height: 90px;
        background: rgba(0, 0, 0, 0.2);
    }
</style>
<section class="content">
    <div class="row " name="availablle_leave_credits">

    </div>

    <div class="box box-primary">
        <div class="box-header with-border">

            <div class="col-lg-12 text-center">  
                <fieldset style="border: 3px solid #2692D0">
                    <legend style=" border-style: none;margin:auto;
                            background-color: #2692D0;
                            color:white;
                            border-width: 0;
                            font-size: 15px;
                            line-height: 20px;
                            margin-bottom: 0;
                            width: auto;
                            padding: 0 10px;
                            border: 1px solid #2692D0;">TIMER</legend>

                    <div class="form-group " id="" style="color:black">
                        <h2 class="text-light-green padding-top-10 h5">00:00:00</h1>
                    </div>

                    <div class="form-group " id="" style="color:black">
                        <button type="button" class="btn btn-success"><i class="icon-copy fa fa-hourglass-start" aria-hidden="true"></i> Start</button>
                        <button type="button" class="btn btn-danger"><i class="icon-copy fa fa-hourglass-start" aria-hidden="true"></i> Stop</button>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="box-header with-border ">
            <div class="col-md-6 ">
                <div class="form-group " id="" style="color:black">
                    <span class="" style="letter-spacing: 0.5px">Overtime From</span>
                    <input type="date" class="form-control" id="worksched_in" onchange="setupPayPeriod()" onkeyup="$('#datefiled_in').val('')" value="<?php echo date('Y-m-d') ?>">
                </div>

            </div>
            <div class="col-md-6 ">
                <div class="form-group" id="" style="color:black">
                    <span class="" style="letter-spacing: 0.5px">Overtime To</span>
                    <input type="date" class="form-control"   id="worksched_out"  >
                </div>
            </div>
            <div class="box-tools pull-right">
            </div>
        </div>
        <div class="box-body">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs " name="request_overtime_tabs" > 

                    <li class="active" name="leave_li" ><a  href="#approve_overtime" data-toggle="tab">Approved Overtime<i  class=""></i></a></li>
                    <li class=""  name="cs_li"><a  href="#pending_overtime" data-toggle="tab">Pending Overtime</i></a></li>
                    <li class=" " name="undertime_li" ><a  href="#denied_overtime" data-toggle="tab">Denied Overtime</a></li>
                </ul>  
                <div class="tab-content" style="color:black">
                    <div class="active tab-pane" id="approve_overtime">
                        <?php $this->load->view('pages/menu/my_account/request_forms/tabs/approved_overtime') ?>
                    </div>
                    <div class="tab-pane" id="pending_overtime">
                        <?php $this->load->view('pages/menu/my_account/request_forms/tabs/pending_overtime') ?>
                    </div>
                    <div class="tab-pane" id="denied_overtime">
                        <?php $this->load->view('pages/menu/my_account/request_forms/tabs/denied_overtime') ?>
                    </div>
                    <div class="tab-pane" id="my_overtime">
                        <?php $this->load->view('pages/menu/my_account/request_forms/tabs/overtime') ?>
                    </div>

                </div>


            </div>


        </div>
        <div class="overlay " name="loading_overlay">
            <i class="fa fa-refresh fa-spin"></i>
        </div>

    </div>



</div>



</section> 
<?php $this->load->view('pages/modal/modal_helpdesk/modal_helpdesk'); ?>      
<script id="signatory-template" type="text/x-custom-template">
    <?php $this->load->view('templates/signatories') ?>
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $(".sidebar-menu").find(".active").removeClass("active");
        $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
        $('#myaccountmenu').addClass('active');
        $('#RequestOvertimeHolder').addClass('active');
        $('#requestovertimecircle').addClass('text-aqua');
        $('#myaccountmenu').addClass('menu-open');
    });
</script>