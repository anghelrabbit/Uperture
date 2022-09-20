
<div class="modal modal-primary fade" name="modal_monthly_restriction">
    <div class="modal-dialog" style="width:1500px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-5 col-md-5" >
                            <div class="form-group">
                                <div class="input-group" style="color:black">
                                    <div class="input-group-addon">
                                        Month
                                    </div>
                                    <select class="form-control" name="onemonth_restrict">
                                        <option value="0">All</option>
                                        <option value="1+January">January</option>
                                        <option value="2+February">February</option>
                                        <option value="3+March">March</option>
                                        <option value="4+April">April</option>
                                        <option value="5+May">May</option>
                                        <option value="6+June">June</option>
                                        <option value="7+July">July</option>
                                        <option value="8+August">August</option>
                                        <option value="9+September">September</option>
                                        <option value="10+October">October</option>
                                        <option value="11+November">November</option>
                                        <option value="12+December">December</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="nav-tabs-custom">
                    <form class="form-horizontal" style="background-color: #357CA5">
                        <div class="nav-tabs-custom">
                            <div class="tab-content" style="color:black">
                                <div class="table-responsive">
                                    <table id="emp_monthly_restrict" class="table table-striped table-bordered" style="width:100%;">
                                        <thead  style="background-color:#357CA5;color:white;">
                                            <tr>
                                                <th>Employee<br>
                                                    <input name="restrict_lastname" class="form-control" type="text" placeholder="Lastname" style="color:black"/>
                                                    <input name="restrict_firstname" class="form-control" type="text"  placeholder="Firstname" style="color:black"/>
                                                </th>
                                                <th >Jan</th>
                                                <th >Feb</th>
                                                <th >Mar</th>
                                                <th >Apr</th>
                                                <th >May</th>
                                                <th >June</th>
                                                <th >July</th>
                                                <th >Aug</th>
                                                <th >Sep</th>
                                                <th >Oct</th>
                                                <th >Nov</th>
                                                <th >Dec</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>



                </div>



            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>