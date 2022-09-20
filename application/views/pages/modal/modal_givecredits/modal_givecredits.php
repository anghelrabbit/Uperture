
<div class="modal modal-primary fade" name="modal_givecredits">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" >Add Leave Credits</h4>
            </div>
            <div class="modal-body">
                <div class="box ">
                    <div class="box-header with-border ">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#employees" data-toggle="tab" style="font-weight: bold;">Employees</a></li>
                            <li class=""><a  href="#credits" name="undertime_signatory" data-toggle="tab" style="font-weight: bold;"> Leave Credits  </a> </li>
                        </ul>   
                    </div>
                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="active tab-pane" id="employees">
                                <form class="form-horizontal" style="background-color: #357CA5">
                                    <div class="nav-tabs-custom">
                                        <div class="tab-content" style="color:black">
                                            <div name="modal_struct_holder">
                                            </div>
                                            <div class="table-responsive">
                                                <?php $this->load->view('templates/selecting_employees'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane" id="credits">
                                <div class="tab-content" style="color:black">
                                    <div class="form-group" name="add_leavetype_table_container">
                                      
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>



            </div>

            <div class="modal-footer">
                <button class="btn btn-success pull-right" style="background-color: #3ED03E; color:white" onclick="updateLeaveCredits()" >Save</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>