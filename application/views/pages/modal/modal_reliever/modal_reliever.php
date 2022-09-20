
<div class="modal modal-primary fade" name="modal_reliever">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true" style="color:black;font-weight: bold">X</span></button>
                <h4 class="modal-title" >Relievers</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div>
                        <?php $this->load->view('templates/structure') ?>
                    </div>
                    <div class="col-lg-12">
                        <div class="table-responsive" >
                            <table id="reliever_table"  class="table  table-bordered" style="color:black;background-color: white" >
                                <thead style="background-color:#2692D0;color:white;">
                                    <tr>
                                        <th ></th>
                                        <th ></th>
                                        <th ><input name='reliever_lastname' type="text" class="form-control" placeholder="Lastname">&nbsp;
                                            <input  name='reliever_firstname' type="text" class="form-control" placeholder="Firstname"></th>
                                        <th >Schedule on <input type="date" class="form-control" value="<?php echo date('Y-m-d') ?>" name="reliever_date"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <span class="btn pull-right" name="reliever_close" style="background-color:#3ED03E">Done</span>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>