/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var structure_template = $('#hidden-template').html();
$(function () {
//    $('input[name=same_time_toggle]').bootstrapToggle();
});
function tabCategory() {
    fetchEmployees(1);
}

function announcementDate() {
    var startDate = new Date($('input[name=announce_datein]').val());
    var endDate = new Date($('input[name=announce_dateout]').val());
    var sameTime = 0;
    var diff = (new Date(endDate - startDate) / 1000 / 60 / 60 / 24) + 1;
    if (endDate == 'Invalid Date' || diff <= 0) {
        endDate = startDate;
        $('input[name=announce_dateout]').val($('input[name=announce_datein]').val());
    } else {
        var endDate = new Date($('input[name=announce_dateout]').val());
    }
    $('tbody[name=datetime_tbody]').empty();

    if ($('input[name=same_time_toggle]').is(':checked')) {
        sameTime = 1;
    }
    if ($('input[name=announce_datein]').val() != '') {

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'Announcement/SetupAnnouncementDates',
            data: {
                datein: $('input[name=announce_datein]').val(),
                dateout: $('input[name=announce_dateout]').val(),
                datetime: JSON.stringify(dates_with_time),
                same_time: sameTime
            }
        }).done(function (result) {
            console.log(result);
            dates_with_time = result;
            addDateTime(dates_with_time);
        });
    }

}

function addDateTime(dates) {
    $.each(dates, function (date, value) {
        var timein = '00:00:00';
        var timeout = '00:00:00';
        if (date in dates_with_time) {
            timein = value['timein'];
            timeout = value['timeout'];
        }
        $('tbody[name=datetime_tbody]').append('<tr>' +
                '<td  style="vertical-align : middle;text-align:center;">' + value['date_string'] + '</td>' +
                '<td><input type="time" class="form-control" onchange="(setTimeInOnDate(' + "'" + date + "','" + 'timein' + "'," + 'this' + '))" value="' + timein + '"  /></td>' +
                '<td><input type="time" class="form-control" onchange="(setTimeInOnDate(' + "'" + date + "','" + 'timeout' + "'," + 'this' + '))" value="' + timeout + '"  /></td>' +
                '</tr>');
    });
}
function setTimeInOnDate(date, index, input) {
    if ($(input).val() != '') {
        dates_with_time[date][index] = $(input).val();
    }

}


function SaveUpdateAnnouncement() {
    console.log(prev_date_time);
    var same_time = 0;
    if ($('input[name=same_time_toggle]').is(':checked')) {
        same_time = 1;
    }
    $('div[name=loading_modal]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'Announcement/SaveUpdateAnnouncement',
        data: {
            id: announce_id,
            dates_with_time: JSON.stringify(dates_with_time),
            prev_date_times: JSON.stringify(prev_date_time),
            selected_departments: JSON.stringify(selected_departments),
            unselected_departments: JSON.stringify(unselected_departments),
            selectd_profileno: JSON.stringify(selected_profileno),
            unselectd_profileno: JSON.stringify(unselected_profileno),
            announcement_topic: $('input[name=announcement_topic]').val(),
            announcement_optional_id: $('input[name=announcement_optional_id]').val(),
            announcement_venue: $('input[name=announcement_venue]').val(),
            announcement_category: $('select[name=announcement_category]').val(),
            announce_datein: $('input[name=announce_datein]').val(),
            announce_dateout: $('input[name=announce_dateout]').val(),
            announcement_description: $('textarea[name=announcement_description]').val(),
            same_time: same_time
        }
    }).done(function (data) {
        $('div[name=loading_modal]').modal('hide');
        if (data['success'] == false) {
            $.each(data['messages'], function (index, value) {
                $('label[name=' + index + '_error]').empty('');
                if (value != '') {
                    $('label[name=' + index + '_error]').removeClass('hidden');
                    $('label[name=' + index + '_error]').append(value);
                }
            });
        } else {
            swal({title: "Success",
                text: "Announcement saved.",
                type: "success",
                show: true,
                backdrop: 'static',
                timer: 1600,
                showConfirmButton: false,
                keyboard: false},
                    function ()
                    {
                        swal.close();
                        backToAnnouncementTable();
                        countNotification();
                    });
        }
    });
}
function selectEmployeesToAnnounce() {
    $('div[name=modal_struct_holder]').empty();
    $('div[name=modal_struct_holder]').append(structure_template);
    FetchRole().done(function () {
        fetchEmployees(1);
        $('div[name=modal_announcement_select_employees]').modal('show');
    });

}
function sameTimeOnChange() {
    announcementDate();
}


function refreshAnnouncementData() {
    selected_departments = {};
    unselected_departments = {};
    selected_profileno = {};
    unselected_profileno = {};
    structure_profileno_selected = {};
    structure_profileno_unselected = {};
    dates_with_time = {};
    employees_table = null;

    $('section[name=announcement_page]').empty();
    $('section[name=announcement_page]').append(announcement_table);
    fetchAnnouncementForms();
}
