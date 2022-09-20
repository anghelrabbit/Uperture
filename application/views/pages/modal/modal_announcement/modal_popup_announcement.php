<div class="modal modal-primary fade" name='modal_popup_announcement'>
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="">Urgent Announcement</h4>
                <input id="" hidden>
            </div>
            <div class='modal-body' >
                <div class="box ">
                    <form class="hidden" action="Announcement" method="POST" name="popup_announcement_form">
                        <input type="hidden" name="idx" value=""/>
                    </form>

                    <div class="box-body" name='popup_announcement_img_body'>
                        <div   id="popup_carousel_announcement" class="carousel slide" data-ride="carousel" data-interval="8000" >
                            <ol class="carousel-indicators" name='popup_announcement_indicators'  >
                            </ol>
                            <div class="carousel-inner" name="popup_announcment_inner">

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>
</div>