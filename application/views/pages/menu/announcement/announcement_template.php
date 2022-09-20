<form name="compose_announcement" action="ComposeAnnouncement" method="POST" target="_blank" >
    <input type="text"  name="data" value="" hidden>

</form>

<div class="box bg-gray" >

    <div class="box-body no-padding">
        <div class="row">

            <div class="col-lg-12 ">
                <div class="pad">
                    <?php if ($this->session->userdata('hr') == 1 || $this->session->userdata('head') == 1) { ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <a class='btn  btn-fw  btn-block' style="background-color:#3ED03E;color:white;letter-spacing: 1px" onclick="createAnnouncement()"> <b>Compose Announcement</b></a>
                            </div>

                        </div>
                    <br>
                    <?php } ?>
                    <div class="table-responsive">
                        <table id="announcement_table" class="table table-striped table-bordered" style="width:100%; background-color:white">
                        <thead style="background-color:#2692D0;color:white;">
                            <tr>
                                <th ></th>
                                <th style="white-space:nowrap;padding-right: 70px">Prepared by:</th>
                                <th style="white-space:nowrap;padding-right: 70px">Topic</th>
                                <th style="white-space:nowrap;padding-right: 70px">Date</th>
                                <th style="white-space:nowrap;padding-right: 70px">Venue</th>
								 <th style="white-space:nowrap;padding-right: 70px">Pop-up</th>
                            </tr>
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>