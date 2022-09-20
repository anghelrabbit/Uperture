<div class="modal modal-primary fade" name='modal_announcement_image'>
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="">Upload Announcement</h4>
                <input id="" hidden>
            </div>
            <div class='modal-body'>
                <input type="file" class=" " name="choose_announcement_img" onchange="" accept="image/x-png,image/jpeg"><br>
                <div name="preview_image" class="" style="height:500px; background: url('') no-repeat center;background-size: cover;background-size:100% 100%"> 
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="col-lg-5">
                                <div class="form-group ">
                                    <label style="color:white;letter-spacing: 1px" >Displayed from</label>
                                    <input type="date" class="form-control  " name="displayed_from">
                                    <label style="color:red;letter-spacing: 1px" name="displayed_from_error" ></label>

                                </div>
                            </div>
                            <div class="col-lg-5 ">
                                <div class="form-group ">
                                    <label style="color:white;letter-spacing: 1px" >Displayed up-to</label>
                                    <input type="date" class="form-control  " name="displayed_to" >
                                    <label style="color:red;letter-spacing: 1px" name="displayed_to_error" ></label>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label style="letter-spacing:0.5px">Pop-up</label><br>
                                    <input name="image_popup" checked type="checkbox" onchange="" >
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <span class="btn btn-primary" style="background-color:#3ED03E" name="save_announcement_img">Save</span>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>