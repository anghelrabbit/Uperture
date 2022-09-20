<section class="content" >

    <div class="box bg-gray" >

        <div class="box-body no-padding">
            <div class="row">
                <div class="pad">

                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table id="requester_table" class="table table-striped table-bordered" style="width:100%; background-color:white">
                                <thead style="background-color:#2692D0;color:white;">
                                    <tr>
                                        <th ></th>
                                        <th style="white-space:nowrap;padding-right: 70px">Category</th>
                                        <th style="white-space:nowrap;padding-right: 70px">Requested by</th>
                                        <th style="white-space:nowrap;padding-right: 70px">Requester Shift</th>
                                        <th style="white-space:nowrap;padding-right: 70px">Reliever Shift</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

<?php $this->load->view('pages/modal/modal_cs/modal_cs'); ?>


<script id="signatory-template" type="text/x-custom-template">
    <?php $this->load->view('templates/signatories') ?>
</script>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        $(".sidebar-menu").find(".active").removeClass("active");
        $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
        $(".sidebar-menu").find(".menu-open").removeClass("menu-open");
        $('#relievermenu').addClass('active');

    });
</script>
