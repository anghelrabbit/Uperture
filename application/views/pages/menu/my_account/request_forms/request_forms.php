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
            <div class="col-md-3">
                <div class="form-group " id="" style="color:black">
                    <span class="" style="letter-spacing: 0.5px">Filed Forms From</span>
                    <input type="date" class="form-control" id="worksched_in" onchange="setupPayPeriod()" onkeyup="$('#datefiled_in').val('')" value="<?php echo date('Y-m-d') ?>">
                </div>

            </div>
            <div class="col-md-3">
                <div class="form-group" id="" style="color:black">
                    <span class="" style="letter-spacing: 0.5px">Filed Forms To</span>
                    <input type="date" class="form-control"   id="worksched_out"  >
                </div>
            </div>
            <div class="box-tools pull-right">
            </div>
        </div>
        <div class="box-body">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs " name="request_forms_tabs" > 

                    <li class="active" name="leave_li" ><a  href="#my_leave" data-toggle="tab">Leave<i  class=""></i></a></li>
                    <!--<li class=""  name="cs_li"><a  href="#my_changesched" data-toggle="tab">Change Schedule</i></a></li>-->
                    <li class=" " name="undertime_li" ><a  href="#my_undertime" data-toggle="tab">Undertime</a></li>
                    <li class=" "  name="ot_li"><a  href="#my_overtime" data-toggle="tab">Overtime</a></li>
                </ul>  
                <div class="tab-content" style="color:black">
                    <div class="active tab-pane" id="my_leave">
                        <?php $this->load->view('pages/menu/my_account/request_forms/tabs/leave') ?>
                    </div>
                    <div class="tab-pane" id="my_undertime">
                        <?php $this->load->view('pages/menu/my_account/request_forms/tabs/undertime') ?>
                    </div>
                    <div class="tab-pane" id="my_changesched">
                        <?php $this->load->view('pages/menu/my_account/request_forms/tabs/change_schedule') ?>
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
        $('#RequestFormHolder').addClass('active');
        $('#requestformcircle').addClass('text-aqua');
        $('#myaccountmenu').addClass('menu-open');
    });
</script>