<section class="content">
    <br>
    <div class="box bg-gray" >

        <div class="box-body no-padding">
            <div class="row">

                <div class="col-lg-12 ">
                    <div class="pad">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <span  class='btn btn-fw btn-block' style="background-color: #3ED03E; color:white; font-size: 18px" name="btn_addcredits"><b>Add Leave Credits</b></span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <span  class='btn btn-fw btn-block' style="background-color: #3ED03E; color:white; font-size: 18px" name="btn_addleavetype"><b>Leave Types</b></span>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table id="employee_leavecredits_table" class="table table-bordered"  style="width:100%; background-color:white">
                                    <thead style="background-color:#2692D0;color:white;">
                                        <tr name="leave_credit_table_tr">
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>



</section>
<?php $this->load->view('pages/modal/modal_givecredits/modal_givecredits'); ?>
<?php $this->load->view('pages/modal/modal_givecredits/modal_add_leavetype'); ?>
<?php $this->load->view('pages/modal/modal_givecredits/modal_update_credits'); ?>

<script id="hidden-template" type="text/x-custom-template">
    <?php $this->load->view('templates/structure') ?>
</script>
<script name="leavetype_template" type="text/x-custom-template">
    <?php $this->load->view('pages/modal/modal_givecredits/modal_leavetype_table_template') ?>
</script>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        $(".sidebar-menu").find(".active").removeClass("active");
        $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
        $(".sidebar-menu").find(".menu-open").removeClass("menu-open");
        $('#leavecreditsmenu').addClass('active');
    });
</script>
