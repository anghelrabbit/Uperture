
<section class="content" name="">
    <div class="box bg-gray" >
        <div class="box-body no-padding">
            <div class="row">
                <div class="col-lg-12 ">
 <?php $this->load->view('templates/structure'); ?>
                    <br>
                    <div class="pad" style="background-color: white">
                        <div class="row">
                            <div class="col-lg-6">
                                <span class="" style="letter-spacing: 0.5px">Schedule</span>
                                <input type="date" class="form-control" id="worksched_in"  value="<?php echo date('Y-m-d') ?>">
                            </div>

                        </div>
                        <br>
                        <div class="table-responsive ">
                           <div  style="max-width: 100%;  overflow: auto;">
                                    <table id="dtr_log_table" class="table  table-bordered" style="width:100%;">
                                        <thead style="background-color:#2692D0;color:white;">
                                            <tr>
                                                <th rowspan="2">Employee</th>
                                                <th colspan="2" style="text-align:center">Time-in</th>
                                                <th colspan="2" style="text-align:center">Time-out</th>
                                            </tr>
                                            <tr>
                                                <th  colspan="1" style="text-align:center">Work Schedule IN</th>
                                                <th  colspan="1" style="text-align:center">Actual Punch IN</th>
                                                <th  colspan="1" style="text-align:center">Work Schedule OUT</th>
                                                <th  colspan="1" style="text-align:center">Actual Punch OUT</th>
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
</section>

<script type="text/javascript">

    document.addEventListener('DOMContentLoaded', function () {
        $(".sidebar-menu").find(".active").removeClass("active");
        $(".sidebar-menu").find(".text-aqua").removeClass("text-aqua");
        $(".sidebar-menu").find(".menu-open").removeClass("menu-open");
        $('#dtrmenu').addClass('active');

    });
</script>