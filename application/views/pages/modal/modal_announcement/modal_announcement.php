<div class="modal modal-primary fade" id="addannouncement">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="announcement_title">Announcement</h4>
                <input id="announce_idholder" hidden>
            </div>
            <form class="form-horizontal" style="background-color: #357CA5">
                <section class="content">

                    <div class="nav-tabs-custom">
                        <ul class="nav  nav-pills ">
                            <li class="active " ><a  href="#details" data-toggle="tab" id="details_tab" onclick="buttonManipulation(1)" >Announcement Details <i  id="announcement_details_error"class=""></i></a></li>
                            <li id="id_employees"><a href="#selectEmployees" data-toggle="tab"  id="employee_tab" onclick="buttonManipulation(2)">Select Participants </a></li>

                        </ul>  
                        <div class="tab-content" style="color:black">
                            <div class="active tab-pane" id="details">
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group ">
                                            <div class="col-lg-6">
                                                <label for="">Topic</label>
                                                <input type="text" class="form-control" id="announceTopic" name="" >
                                                <label id='topicerror' style='color:red;' hidden></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <label for="announceID">Announcement ID:</label>
                                                <div class="input-group " id="" style="color:black">
                                                    <div class="input-group-addon">
                                                        <span class="" id="defaultID">Default ID</span>
                                                    </div>
                                                    <input type="text" class="form-control" id="announceID" name="announceID" placeholder="Announcement ID">
                                                </div>
                                                <label id='announceerror' style='color:red;' hidden></label>
                                            </div>
                                        </div>
                                    </div>


                                </div>


                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="form-group ">
                                            <div class="col-lg-6">
                                                <label for="announceWherere">Where</label>
                                                <input type="text" class="form-control" id="announceWherere" name="announceWherere" >
                                                <label id='whereerror' style='color:red;' hidden></label>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group ">
                                                    <div class="col-lg-12">
                                                        <label for="announceWherere">Announcement Type</label>
                                                        <select class="form-control" id="announce_category">
                                                            <option value="0">Ordinary</option>
                                                            <option value="1">Office Order</option>
                                                            <option value="2">Seminar</option>
                                                            <option value="3">Weekly Meeting</option>
                                                            <option value="4">Urgent Announcement</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class=" row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="reliever_sched_table" class="table table-striped table-bordered table-hover" style="width:100%;">
                                                <thead style="background-color:#126B95;color:white;">
                                                    <tr>
                                                        <th colspan="2"> 
                                                            Date
                                                        </th>
                                                        <th colspan="2"> 
                                                            Time
                                                        </th >
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div class="input-group " id="" style="color:black">
                                                                <div class="input-group-addon">
                                                                    <i class="">From</i>
                                                                </div>
                                                                <input type=""  class="form-control " id="startDate"data-toggle="tooltip">
                                                                <div class="input-group-addon">
                                                                    <i class="fa fa-calendar"></i>
                                                                </div>
                                                            </div>
                                                            <label id='startdate_error' style='color:red;' hidden></label>
                                                        </td>
                                                        <td>
                                                            <div class="input-group " id="" style="color:black">
                                                                <div class="input-group-addon">
                                                                    <i class="">To</i>
                                                                </div>
                                                                <input  type="" class="form-control " id="endDate"data-toggle="tooltip">
                                                                <div class="input-group-addon">
                                                                    <i class="fa fa-calendar"></i>
                                                                </div>
                                                            </div>
                                                            <label id='enddate_error' style='color:red;' hidden></label>
                                                        </td>
                                                        <td>
                                                            <div class="input-group ">
                                                                <div class="input-group-addon">
                                                                    <i class="">From</i>
                                                                </div>
                                                                <input type="text" class="form-control pull-right timepicker" id="startTime"  onkeyup="timeKeyUp(event, 'startTime')" onkeydown="timeKeyDown('startTime')">
                                                                <div class="input-group-addon">
                                                                    <i class="fa fa-clock-o"></i>
                                                                </div>
                                                            </div>
                                                            <label id='starttimeerror' style='color:red;' hidden></label>
                                                        </td>
                                                        <td>
                                                            <div class="input-group ">
                                                                <div class="input-group-addon">
                                                                    <i class="">To</i>
                                                                </div>
                                                                <input type="text" class="form-control pull-right timepicker" id="endTime"  onkeyup="timeKeyUp(event, 'endTime')" onkeydown="timeKeyDown('endTime')">
                                                                <div class="input-group-addon">
                                                                    <i class="fa fa-clock-o"></i>
                                                                </div>
                                                            </div>
                                                            <label id='endtimeerror' style='color:red;' hidden></label>
                                                    </tr>  
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group ">
                                            <div class="col-lg-12">
                                                <label for="announceWhat">Details:</label>
                                                <textarea rows="4" class="form-control" id="announceWhat" name="announceWhat" style="resize:none"></textarea>
                                                <label id='whaterror' style='color:red;' hidden></label>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div class=" tab-pane" id="selectEmployees">
                                <div class="row" id="category_holder">
                                    <div class="col-md-12 ">
                                        <div class="form-group ">
                                            <div class="col-md-2" id="company_div" >
                                                <small><b>Company</b></small>
                                                <select class="form-control form-control-sm" id="request_company" name="" onchange="tabCategory('');">
                                                </select>
                                            </div>
                                            <div class="col-md-2" id="location_div">
                                                <small><b>Location</b></small>
                                                <select class="form-control form-control-sm" id="request_location" name="" onchange="tabCategory('');">

                                                </select>
                                            </div>

                                            <div class="col-md-2 " id="division_div">
                                                <small><b>Division</b></small>
                                                <select class="form-control form-control-sm" id="request_division" name="" onchange="tabCategory('');">

                                                </select>
                                            </div>
                                            <div class="col-md-2" id="department_div">
                                                <small><b>Department</b></small>
                                                <select class="form-control form-control-sm" id="request_department" name="" onchange="tabCategory('');">
                                                </select>
                                            </div>
                                            <div class="col-md-2" id="section_div">
                                                <small><b>Section</b></small>
                                                <select class="form-control form-control-sm" id="request_section" name="" onchange="tabCategory('');">
                                                </select>
                                            </div>
                                            <div class="col-md-2" id="area_div">
                                                <small><b>Area</b></small>
                                                <select class="form-control form-control-sm" id="request_area" name="" onchange="tabCategory('');">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group " id="add_announcement_table">
                                            <div class="col-lg-12">
                                                <div class="table-responsive">
                                                    <div id="employees_side">
                                                        <table id="select_employees_table" class="table  table-bordered" style="width:100%;">
                                                            <thead style="background-color:#357CA5;color:white;">
                                                                <tr>
                                                                    <th>
                                                                        <input type="checkbox" id="check_all" onchange="check_all_emp('check_all')">
                                                                    </th>
                                                                    <!--<th>ID</th>-->
                                                                    <th>Employee</th>
                                                                    <th>Job Position</th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                    <div id="participants_side">
                                                        <table id="participants_table" class="table  table-bordered" style="width:100%;">
                                                            <thead style="background-color:#357CA5;color:white;">
                                                                <tr>
                                                                    <th>Employee</th>
                                                                    <th>Job Position</th>
                                                                    <th>Confirmation</th>
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
            </form>
            <div class="modal-footer">
                <?php if ($this->session->userdata('hr') == 1 || $this->session->userdata('head') == 1 || $this->session->userdata('admin') == 1) { ?>
                    <button type="button" id="btnProceed" class="btn btn-success mr-2" style="float:right;" onclick="proceedNextStep()">Proceed</button>
                    <button type="button" id="btnSaveAnnounce" class="btn btn-success mr-2" style="float:right;" onclick="saveUpdateAnnouncement()">Save</button>
                    <button type="button" id="btnUpdateDetails" class="btn btn-success mr-2" style="float:right;" onclick="saveUpdateAnnouncement()">Update</button>
                    <button type="button" id="btnRemoveDetails" class="btn btn-success mr-2" style="float:right;" onclick="">Remove</button>
                <?php } ?>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>