<div class="modal modal-primary fade" name="modal_scheduler" tabindex="-1"  role="dialog"  aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" name="btn_close_modal_scheduler" style="background-color:white">
                    <span aria-hidden="true" style="color:black;font-weight: bold">X</span></button>
                <div >
                    <span class="" name="undertime_title" style="letter-spacing:0.7px;font-size:20px">Setup Schedule</span>&nbsp;&nbsp;
                    <!--<span class="btn badge" name='undertime_helpdesk' style="background-color:#3ED03E;height:25px;width:25px;font-size:15px;margin-top: -7px">?</span>-->
                </div>
            </div>
            <div class="modal-body" >
                <div class="box-header with-border " style="background-color:white">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#employees" data-toggle="tab" style="font-weight: bold;">Employees</a></li>
                        <li class=""><a  href="#setup_schedule" name="" data-toggle="tab" style="font-weight: bold;"> Setup Schedule  </a> </li>

                    </ul>   
                </div>
                <div class="nav-tabs-custom">
                    <div class="tab-content">
                        <div class="active tab-pane" id="employees">
                            <?php $this->load->view('pages/modal/modal_scheduler/tabs/employees') ?>
                        </div>

                        <div class="tab-pane" id="setup_schedule">
                            <?php $this->load->view('pages/modal/modal_scheduler/tabs/setup_schedule') ?>

                        </div>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <span class="btn pull-right "  name="save_schedule" style="background-color:#3ED03E;">Save</span>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>