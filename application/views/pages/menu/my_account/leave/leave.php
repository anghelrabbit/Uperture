
<style>
 .button {
  padding: 15px 25px;
  font-size: 20px;
  text-align: center;
  cursor: pointer;
  outline: none;
  color: #fff;
  background-color: #3ED03E;
  border: none;
  border-radius: 15px;
  box-shadow: 0 9px #999;
  margin-top:5%
}

.button:hover {background-color: #3e8e41; color:white}

.button:active {
  background-color: #3e8e41;
  box-shadow: 0 5px #666;
   color: white;
  transform: translateY(4px);
}

</style>
<section class="content">


    <?php $col = "col-lg-12 col-md-12 col-sm-12" ?>
    <?php if ($this->session->userdata('admin') == 1 || $this->session->userdata('hr') == 1  || $this->session->userdata('scheduler') ==1 ) { ?>
        <?php $col = "col-lg-3 col-md-12 col-sm-12 col-xs-12"; ?>
    <?php } ?>
    <div class="row">
        <div class="<?php echo $col ?>" >
            <button type="button" class="button btn btn-lg col-lg-12 col-sm-12 col-xs-12 col-md-12 "  onclick="">
                <b><i class="fa fa-calendar-plus-o "></i> File Leave </b>
            </button>
        </div>
        <div class="<?php echo $col ?>">
            <button type="button" id='sample' class="button btn btn-lg col-lg-12 col-sm-12 col-xs-12 col-md-12"   onclick="openLeaveLedger()">
                <b><i class="fa fa-book"></i> Leave Ledger </b>
            </button>
        </div>
        <?php if ($this->session->userdata('admin') == 1 || $this->session->userdata('hr') == 1  || $this->session->userdata('scheduler') ==1 ){ ?>
            <div class="<?php echo $col ?>">
                <button type="button"  class="button btn btn-lg col-lg-12 col-sm-12 col-xs-12 col-md-12"  onclick="openGiveCredits()">
                    <b><i class="fa fa-edit "></i> Add Leave Credits </b>
                </button>
            </div>
            <div class="<?php echo $col ?>">
                <button type="button"  class="button btn btn-lg col-lg-12 col-sm-12 col-xs-12 col-md-12"  onclick="show_close_credits()">
                    <b><i class="fa fa-calendar-check-o "></i> Write-off Credits </b>
                </button>
            </div>
        <?php } ?>

    </div>  
    <br>
    <div class="form-group">
        <div class="nav-tabs-custom">

            <div class="tab-content">
                <div class="box-header with-border">    
                </div> 
                <div class="table-responsive">
                    <table id="pending-leave-table" class="table table-striped table-bordered" style="width:100%">
                        <thead style="background-color:#2692D0;color:white;">
                            <tr>
                                <th>Action
                                    <br>
                                    <select class="form-control" id="select_leavestatus" onchange="leave.fetch_pending_leave()">
                                        <option value="">All</option>
                                        <option value="0">Pending</option>
                                        <option value="1">Approved</option>
                                        <option value="2">Declined</option>
                                    </select>
                                </th>
                                <th style="white-space:nowrap;padding-right: 150px">Dates</th>
                                <th style="white-space:nowrap;padding-right: 150px">Type of Leave
                                    <br>
                                    <select class="form-control" id="select_leavetype" onchange="leave.fetch_pending_leave()">
                                        <option value="">All</option>
                                        <option value="0">Vacation Leave</option>
                                        <option value="1">Sick Leave</option>
                                        <option value="2">Others</option>
                                    </select>
                                </th>
                                <th style="white-space:nowrap;padding-right: 150px">Days</th>
                                <th style="white-space:nowrap;padding-right: 150px">Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>                        





            </div>
        </div>
    </div>

</section>
   <?php $this->load->view('pages/modal/modal_givecredits/modal_givecredits'); ?>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        $(".sidebar-menu").find(".active").removeClass("active");
        $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
        $('#myaccountmenu').addClass('active');
        $('#leaveHolder').addClass('active');
        $('#leavecircle').addClass('text-aqua');
        $('#myaccountmenu').addClass('menu-open');
        $('.button').mouseout(function(){
            $('.button').css('color', 'white');
        });
    });
</script>
