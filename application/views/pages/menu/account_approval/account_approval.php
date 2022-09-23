
<section class="content" >
    <?php // $this->load->view('templates/structure') ?>
    <br>

    <div class="box box-primary" name="dashboard_box">

        <div class="box-header with-border">






        </div>
        <div class="box-body no-padding">
            <div class="row">

                <div class="col-lg-12 col-xs-12 col-md-12">
                    <div class="pad">
                        <div class="nav-tabs-custom " id="table_tabs">
                            <div class="tab-content">
                                <div class="active tab-pane" id="pendingleave" >
                                    <div class="box-body" style="max-width: 100%;  overflow: auto;">
                                        <table id="account_approve_table" class="table table-responsive  table-hover" >
                                            <thead id="leave_thead" style="background-color:#2692D0;color:white;">
                                                <tr>
                                                    <th style="padding-right: 70px">Activate?</th>
                                                    <th >Name</th>
                                                    <!--<th style="white-space:nowrap;padding-right: 100px">Address</th>-->
                                                    <th style="white-space:nowrap;padding-right: 150px">Email<br> 
                                                    <th style="white-space:nowrap;padding-right: 150px">Contact Number</th>
                                                       </th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>
<?php $this->load->view('pages/modal/modal_approve_account/modal_add_account_details'); ?>


<script id="signatory-template" type="text/x-custom-template">
    <?php $this->load->view('templates/signatories') ?>
</script>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        $(".sidebar-menu").find(".active").removeClass("active");
        $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
        $(".sidebar-menu").find(".menu-open").removeClass("menu-open");
        $('#accountapprovalmenu').addClass('active');

    });
</script>

