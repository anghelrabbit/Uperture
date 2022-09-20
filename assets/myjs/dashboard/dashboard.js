var sub_category = 1;
tab_category = 0;
var empcount = 0;
var result = '';

var announcement_id = 0;
isclicked = 0;
$(function () {
    $('input[name=image_popup]').bootstrapToggle();
    var d = new Date();
    d = d.getMonth();
    if (d < 10) {
        d = "0" + d;
    }
    $('select[name=birthday_month]').val(d);
    fetchAnnouncement();

});






function tabCategory() {
    countAllEmployees();
}






function previewImage() {
    var reader = new FileReader();
    reader.onload = function (e) {
        $('div[name=preview_image]').css({
            'background': 'url(' + e.target.result + ') no-repeat center',
            'background-size': 'cover',
            'background-size': '100% 100%'
        }).fadeIn('slow');
    };
    reader.readAsDataURL($('input[name=choose_announcement_img]').prop('files')[0]);
}

function fetchAnnouncement() {
    $.ajax({
        type: 'POST',
        url: 'Announcement/FetchAnnouncement',
        dataType: 'json'
    }).done(function (result) {
		console.log(result);
        var count = result['images'].length + result['announcement'].length;
        if (count > 0) {
            var pop_details = '';
            var popup_indicator = '';
            var popup_inner = '';
            if (result['popup_announcement'].length > 0) {
                pop_details = ',#popup_carousel_announcement';
                popup_indicator = ',ol[name=popup_announcement_indicators]';
                popup_inner = ',div[name=popup_announcment_inner]';
                $('div[name=modal_popup_announcement]').modal('show');
            }
            $("#carousel_announcement" + pop_details).carousel("pause").removeData();
            $('div[name=announcment_inner], ol[name=announcement_indicators]' + popup_indicator + popup_inner).empty();
            refreshAnnouncementCarousel();
            dashboardAnnouncements(result, popup_indicator, popup_inner, result['hr']);
            $('span[name=announcement_badge]').removeClass('hidden');
            $('span[name=announcement_badge]').empty();
            $('span[name=announcement_badge]').append(count);
        } else {

            $('div[name=announcement_img_body]').empty();
            $('div[name=announcement_img_body]').append(
                    '<div id="carousel_announcement" class="carousel slide" data-ride="carousel" data-interval="8000">' +
                    '<ol class="carousel-indicators" name="announcement_indicators">' +
                    '</ol>' +
                    ' <div class="carousel-inner" name="announcment_inner">' +
                    '</div>' +
                    ' </div>'
                    );
            $('span[name=announcement_badge]').addClass('hidden');
            $('div[name=announcment_inner]').empty();
            $('div[name=announcment_inner]').append('No available data');
            $('ol[name=announcement_indicators]').empty();
        }
    });
}
function refreshAnnouncementCarousel() {
    $('div[name=announcement_img_body], div[name=popup_announcement_img_body]').empty();
    $('div[name=announcement_img_body]').append(
            '<div id="carousel_announcement" class="carousel slide" data-ride="carousel" data-interval="8000">' +
            '<ol class="carousel-indicators" name="announcement_indicators">' +
            '</ol>' +
            ' <div class="carousel-inner" name="announcment_inner">' +
            '</div>' +
            '<a class="left carousel-control" href="#carousel_announcement" data-slide="prev" style="width:5%;color:#2692D0">' +
            '<i class="fa fa-angle-left"></i>' +
            '</a>' +
            '<a class="right carousel-control" href="#carousel_announcement" data-slide="next" style="width:10%;color:#2692D0">' +
            '<i class="fa fa-angle-right"></i>' +
            '</a>' +
            ' </div>'
            );
    $('div[name=popup_announcement_img_body]').append(
            '<div id="popup_carousel_announcement" class="carousel slide" data-ride="carousel" data-interval="8000">' +
            '<ol class="carousel-indicators" name="popup_announcement_indicators">' +
            '</ol>' +
            ' <div class="carousel-inner" name="popup_announcment_inner">' +
            '</div>' +
            '<a class="left carousel-control" href="#popup_carousel_announcement" data-slide="prev" style="width:5%;color:#2692D0">' +
            '<i class="fa fa-angle-left"></i>' +
            '</a>' +
            '<a class="right carousel-control" href="#popup_carousel_announcement" data-slide="next" style="width:10%;color:#2692D0">' +
            '<i class="fa fa-angle-right"></i>' +
            '</a>' +
            ' </div>'
            );

}



function dashboardAnnouncements(data, popup_indicator, popup_inner, hr) {
    var active = '';
    var active2 = '';
    var counter = 0;
    var counter2 = 0;


    for (var cv = 0; cv < data['images'].length; cv++) {
        var buttons = '';
        if (hr == 1) {
            buttons = '<span class="btn" style="padding:0;border-radius: 50%; width: 50px;height: 50px;margin:0; background-color:#FF392E;color:white;margin-left:10px" onclick="removeAnnouncement(' + data['images'][cv]['id'] + ",'" + data['images'][cv]['name'] + "'" + ')"><i class="glyphicon glyphicon-trash" style="margin-top:13px;margin-left:-2px;font-size:20px"></i></span>';
        }
        active = (counter == 0) ? ' active' : '';
        if (data['images'][cv]['pop_up'] == 1) {
            active2 = (counter2 == 0) ? ' active' : '';
            $('ol[name=popup_announcement_indicators]').append('<li style="background-color:#3C8DBC" data-target="#popup_carousel_announcement" data-slide-to="' + counter2 + '" class="' + active2 + '"></li>&nbsp;');
            announcement_inner_images('popup_announcment_inner', counter2, active2, data, cv, buttons);
            counter2++;
        }
        $('ol[name=announcement_indicators]').append('<li style="background-color:#3C8DBC" data-target="#carousel_announcement" data-slide-to="' + counter + '" class="' + active + '"></li>&nbsp;');
        announcement_inner_images('announcment_inner', counter, active, data, cv, buttons);
        counter++;

    }

    for (var cv = 0; cv < data['announcement'].length; cv++) {
        active = (counter == 0) ? ' active' : '';
        if (data['announcement'][cv]['pop_up'] == 1) {
            active2 = (counter2 == 0) ? ' active' : '';
            $('ol[name=popup_announcement_indicators]').append('<li style="background-color:#3C8DBC" data-target="#popup_carousel_announcement" data-slide-to="' + counter2 + '" class="' + active2 + '"></li>&nbsp;');
             announcement_inner_announce('popup_announcment_inner', active, data, cv);
            counter2++;
        }
        $('ol[name=announcement_indicators]').append('<li style="background-color:#3C8DBC" data-target="#carousel_announcement" data-slide-to="' + counter + '" class="' + active + '"></li>&nbsp;');
        announcement_inner_announce('announcment_inner', active, data, cv);
        counter++;
    }
}

function announcement_inner_images(index, counter, active, data, cv, buttons) {
    $('div[name=' + index + ']').append("<div onmouseover='updateAnnouncementImages(" + counter + "," + '1' + ")' onmouseout='updateAnnouncementImages(" + counter + "," + '0' + ")' class='item" + active + "' style=" + '"' + "height:400px; background: url('assets/uploads/announcement/" + data['images'][cv]['name'] + "') no-repeat center;background-size: cover;background-size:100% 100%" + '"' + ">" +
            ' <div name="div_img_' + counter + '" class="row hidden" style="position:absolute;bottom:15%;left:45%">' +
            buttons +
            ' </div>' +
            '</div>');
}

function announcement_inner_announce(index, active, data, cv) {
    $('div[name=' + index + ']').append(
            '<div class="item ' + active + '" style="height:400px;' +
            'background: url(' + "'" + 'assets/images/announcement_background.jpg' + "'" + ') no-repeat center;background-size: cover;background-size:100% 100%">' +
            '<div class="row" style="display: flex;align-items: center;justify-content: center;height:500px;">' +
            '<div >' +
            '<div  style="text-align:center;letter-spacing: 2px">' +
            '<span style="font-size:40px;font-weight: bold;color:#FFFF00">' + data['announcement'][cv]['topic'] + '</span>' +
            '</div>' +
            '<div style="text-align:center;letter-spacing: 1px">' +
            '<span style="font-size:20px;font-weight: bold;">Venue: ' + data['announcement'][cv]['venue'] + '</span>' +
            '</div >' +
            '<div style="text-align:center;letter-spacing: 1px">' +
            '<span style="font-size:15px;font-weight: bold;">' + data['announcement'][cv]['dates'] + '</span>' +
            '</div>' +
            '<br>' +
            '<div class="col-lg-12" style="margin-top:5%;text-align: center">' +
            '<span class="btn" style="background-color:#3ED03E;color:white" onclick="redirectToAnnouncementPage(' + "'" + data['announcement'][cv]['announcement_id'] + "'" + ')">Show Details</span>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>');
}


function updateAnnouncementImages(counter, show) {
    if (show == 1) {
        $('div[name=div_img_' + counter + ']').removeClass('hidden');
    } else {

        $('div[name=div_img_' + counter + ']').addClass('hidden');
    }
}

$('input[name=choose_announcement_img]').change(function () {
    previewImage();
});

$('span[name=btn_announcement_img]').click(function () {
    $('div[name=modal_announcement_image]').modal('show');
    $('label[name=displayed_from_error]').empty();
    $('label[name=displayed_to_error]').empty();
    $('input[name=choose_announcement_img]').val('');
    $('input[name=displayed_from]').val('');
    $('input[name=displayed_to]').val('');
    $('div[name=preview_image]').css(
            {
                'background': 'url("") no-repeat center',
                'background-size': 'cover',
//                'filter':'grayscale(100%)',
                'background-size': '100% 100%'
            }).fadeIn('slow');
});

$('span[name=save_announcement_img]').click(function () {
    var fileToUpload = $('input[name=choose_announcement_img]').prop('files')[0];
    var for_popup = ($('input[name=image_popup]').is(':checked')) ? 1 : 0;

    var form_data = new FormData();
    form_data.append("file", fileToUpload);
    form_data.append("displayed_from", $('input[name=displayed_from]').val());
    form_data.append("displayed_to", $('input[name=displayed_to]').val());
    form_data.append("popup", for_popup);
    $.ajax({
        type: 'POST',
        url: "Announcement/SaveAnnouncementImage",
        data: form_data,
        contentType: false,
        cache: false,
        processData: false,
        dataType: 'json'
    }).done(function (data) {
        if (data['success'] == false) {
            $.each(data['messages'], function (index, value) {
                if (value != '') {
                    $('label[name=' + index + '_error]').removeClass('hidden');
                    $('label[name=' + index + '_error]').empty('');
                    $('label[name=' + index + '_error]').append(value);
                }
            });
        } else {
            countNotification();
            $('div[name=modal_announcement_image]').modal('hide');
            fetchAnnouncement();
        }
    });

});
$('select[name=birthday_month]').change(function () {
    fetchEmployee(0);
    $('#restartConfetti').click();
    setTimeout(function () {
        $('#stopConfetti').click();
    }, 4000);
});


function redirectToAnnouncementPage(id) {
    $('input[name=idx]').val(id);
    $('form[name=announcement_form]').submit();
}

function birthdaysReport() {
    $('input[name=bday_month]').val($('select[name=bday_month]').val());
    $('#generate_bday_report').submit();
}


$('span[name=btn_bday_report]').click(function () {
    birthdaysReport();
});



function removeAnnouncement(id, name) {
    swal({
        title: "Are you sure?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        confirmButtonText: "Yes Proceed.",
        closeOnConfirm: false
    }, function () {
        $.ajax({
            type: 'POST',
            data: {announcement_id: id, img_name: name},
            url: 'Announcement/RemoveAnnouncementImage',
            dataType: 'json'
        }).done(function (result) {
            if (result) {
                swal({title: "Success",
                    text: "Announcement removed.",
                    type: "success",
                    show: true,
                    backdrop: 'static',
                    timer: 1600,
                    showConfirmButton: false,
                    keyboard: false},
                        function ()
                        {
                            countNotification();
                            fetchAnnouncement();
                            swal.close();
                        });
            }
        });
    });
}

$('span[name]').click(function () {
    $.ajax({
        type: 'POST',
        data: {username: $('input[name=emp_user]').val()},
        url: 'Dashboard/ShowEmp',
        dataType: 'json'
    }).done(function (result) {
        alert(result);
    });
});
