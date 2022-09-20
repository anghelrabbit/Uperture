


<div class="row">
    <div class="col-lg-5">
        <div class="col-lg-12">
            <div class="box box-primary" >
                <div class="box-header with-border">
                    <div class="row">
                        <div class="col-lg-12">
                        </div>
                    </div>
                </div>
                <div class="box-body no-padding">
                    <form class="form-horizontal">
                        <div class="row">
                            <div class="col-lg-12 ">
                                <div class="pad">
                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <div class="form-group ">
                                                <div class="col-lg-6">
                                                    <label for="">Topic</label>
                                                    <input type="text" class="form-control"  name="announcement_topic" >
                                                    <label name='announcement_topic_error' style='color:red;' ></label>
                                                </div>
                                                <div class="col-lg-6">
                                                    <label for="announceID">Announcement ID:</label>
                                                    <div class="input-group " id="" style="color:black">
                                                        <div class="input-group-addon">
                                                            <span class="" id="defaultID">Default ID</span>
                                                        </div>
                                                        <input type="text" class="form-control"  name="announcement_optional_id" placeholder="Announcement ID">
                                                    </div>
                                                    <label name='announcement_optional_id_error' style='color:red;' ></label>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group ">
                                                <div class="col-lg-6">
                                                    <label >Zoom Link</label>
                                                    <input type="text" class="form-control"  name="announcement_venue" >
                                                    <label name='announcement_venue_error' style='color:red;' ></label>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group ">
                                                        <div class="col-lg-12">
                                                            <label>Type</label>
                                                            <select class="form-control" name="announcement_category">
                                                                <option value="0">Ordinary</option>
                                                                <option value="1">Office Order</option>
                                                                <option value="2">Seminar</option>
                                                                <option value="3">Weekly Meeting</option>
                                                                <option value="4">Urgent Announcement</option>
                                                                <option value="5">Activity</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>

            </div>

        </div>


        <div class="col-lg-12">
            <div class="box box-solid">
                <div class="box-header with-border " style="background-color: #2692D0;color:white">
                    <h3 class="box-title">When </h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                            <div>
                                <label >Start</label>
                                <input type="date" class="form-control pull-right"  name="announce_datein" onchange="announcementDate()">
                            </div>
                            <label name="announce_datein_error" style="color:red"></label>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                            <div>
                                <label >End</label>
                                <input type="date" class="form-control"  name="announce_dateout" onchange="announcementDate()">
                            </div>
                            <label name="announce_dateout_error" style="color:red"></label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">

                            <label for="same_time_toggle">Same Time</label>
                            <input name="same_time_toggle" checked type="checkbox" onchange="sameTimeOnChange()">
                        </div>
                    </div>
                    <br>
                    <div class="table-responsive" style="height: 100%;max-height: 370px; overflow-y: auto; overflow-x: hidden;">
                        <label name="announcement_datetime_error" style="color:red;display: flex;  align-items: center;justify-content: center;"></label>
                        <table class="table table-bordered"  style="width:100%; background-color:white">
                            <thead style="background-color:#2692D0;color:white;">
                                <tr>
                                    <th colspan="1" style="text-align:center" rowspan="2" >Date</th>
                                    <th colspan="2" style="text-align:center">Time</th>
                                </tr>
                                <tr>
                                    <th  colspan="1" style="text-align:center">Time-in</th>
                                    <th  colspan="1" style="text-align:center">Time-out</th>
                                </tr>
                            </thead>
                            <tbody name="datetime_tbody" >

                            </tbody>
                        </table>
                    </div>


                </div>

            </div>
        </div>


    </div>
    <div class="col-lg-7">
        <div class="col-lg-12">
            <?php if ($this->session->userdata('hr') == 1) { ?>
                <div class="form-group">
                    <span class="btn btn-block" style="background-color:#2692D0;color:white" name="select_employees_btn" onclick="selectEmployeesToAnnounce()">Select Employees</span>
                    <label name="announcement_participants_error" style="color:red;display: flex;  align-items: center;justify-content: center;"></label>
                </div>
            <?php } ?>
        </div>
        <br>
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Description</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="form-group">
                        <textarea id="compose-textarea" class="form-control" style="height: 400px" name="announcement_description">
                    
                        </textarea>
                    </div>
                    <!--                    <div class="form-group">
                                            <div class="btn btn-default btn-file">
                                                <i class="fa fa-paperclip"></i>Attachment
                                                <input type="file" name="something_sample">
                                            </div>
                                        </div>-->
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <?php if ($this->session->userdata('hr') == 1) { ?>
                    <div class="pull-right">
                        <button  class="btn btn-block" name="save_announcement" style="background-color:#3ED03E;color:white" onclick=" SaveUpdateAnnouncement();">Save</button>
                        <button  class="btn" name="update_announcement" style="background-color:#F87D42;color:white" onclick="SaveUpdateAnnouncement();">Update</button>
                        <button  class="btn" name="remove_announcement" style="background-color:#F8665E;color:white" onclick="">Remove</button>
                    </div>
                    <?php }?>
                </div>
                <!-- /.box-footer -->
            </div>

        </div>
    </div>
</div>

