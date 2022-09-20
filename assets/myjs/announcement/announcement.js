/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var dates_with_time = {};
var prev_date_time = {};
var announcement_table = $('script[name=announcement_table]').html();
var announcement_compose = $('script[name=announcement_compose]').html();
var announce_id = 0;
$(function () {
    $('section[name=announcement_page]').empty();
    $('section[name=announcement_page]').append(announcement_table);
    fetchAnnouncementForms();
    if (parseInt(extra_data) != 0) {
        showAnnouncementDetails(parseInt(extra_data));
    }

});

function createAnnouncement() {
    $('section[name=announcement_page]').empty();
    $('section[name=announcement_page]').append(announcement_compose);
    $('input[name=same_time_toggle]').bootstrapToggle();
    $('h1[name=page_title]').empty();
    $('h1[name=page_title]').append('<span class="btn" style="background-color:#F8665E;color:white" onclick="backToAnnouncementTable()"><i class="glyphicon glyphicon-chevron-left"></i>Back</span>&nbsp;Compose Announcement');
    $('button[name=remove_announcement]').addClass('hidden');
    $('button[name=update_announcement]').addClass('hidden');
    selected_departments = {};
    unselected_departments = {};
    selected_profileno = {};
    unselected_profileno = {};
    structure_profileno_selected = {};
    structure_profileno_unselected = {};
    dates_with_time = {};
    prev_date_time = {};
    employees_table = null;
}

function fetchAnnouncementForms() {

    $('#announcement_table').DataTable().clear().destroy();
    $('#announcement_table').DataTable({
//        dom: 'frtip',
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],

        ajax: {
            url: 'Announcement/FetchAnnouncementForms',
            type: 'POST',

        },
		 initComplete: function (settings, json) {
            $('.announce_toggle').bootstrapToggle();
        }

    }
    );
    $('#announcement_table_length').addClass('hidden');
    $('#announcement_table_filter').addClass('hidden');
}

function backToAnnouncementTable() {
    $('section[name=announcement_page]').empty();
    $('section[name=announcement_page]').append(announcement_table);
    refreshAnnouncementData();
    fetchAnnouncementForms();
    extra_data = 0;
    $('h1[name=page_title]').empty();
    $('h1[name=page_title]').append('Announcement');
}

function showAnnouncementDetails(id) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'Announcement/FetchSpecificAnnouncement',
        data: {
            id: id,
        }
    }).done(function (data) {
        console.log(data);
        $('section[name=announcement_page]').empty();
        $('section[name=announcement_page]').append(announcement_compose);
        $('input[name=same_time_toggle]').bootstrapToggle();
        if (data['same_time'] == 0) {
            $('input[name=same_time_toggle]').bootstrapToggle('toggle');
        }
        announce_id = data['announcement_info']['id'];
        $('input[name=announcement_topic]').val(data['announcement_info']['topic']);
        $('input[name=announcement_optional_id]').val(data['announcement_info']['optional_id']);
        $('input[name=announcement_venue]').val(data['announcement_info']['venue']);
        $('select[name=announcement_category]').val(data['announcement_info']['announcement_type']);
        $('input[name=announce_datein]').val(data['datein']);
        $('input[name=announce_dateout]').val(data['dateout']);
        $('textarea[name=announcement_description]').val(data['announcement_info']['description']);

        $('h1[name=page_title]').empty();
        $('h1[name=page_title]').append('<span class="btn" style="background-color:#F8665E;color:white" onclick="backToAnnouncementTable()"><i class="glyphicon glyphicon-chevron-left"></i>Back</span>&nbsp;' + data['announcement_info']['topic']);

        $('button[name=remove_announcement]').removeClass('hidden');
        $('button[name=update_announcement]').removeClass('hidden');
        $('button[name=save_announcement]').addClass('hidden');
        selected_profileno = data['participants'];
        structure_profileno_selected = data['structure_profileno'];
        dates_with_time = data['datetime'];
        prev_date_time = data['datetime'];
        $.each(dates_with_time, function (index, value) {
            $('tbody[name=datetime_tbody]').append('<tr>' +
                    '<td  style="vertical-align : middle;text-align:center;">' + value['date_string'] + '</td>' +
                    '<td><input type="time" class="form-control" onchange="(setTimeInOnDate(' + "'" + index + "','" + 'timein' + "'," + 'this' + '))" value="' + value['timein'] + '" /></td>' +
                    '<td><input type="time" class="form-control" onchange="(setTimeInOnDate(' + "'" + index + "','" + 'timeout' + "'," + 'this' + '))"  value="' + value['timeout'] + '" /></td>' +
                    '</tr>');
        });
    });
}
$('button[name=btn_create_announcement]').on('click', function () {
    createAnnouncement();
    announce_id = 0;
});

function popupAnnouncement(id, index) {
    var is_checked = ($(index).is(':checked')) ? 1 : 0;
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'Announcement/AnnouncementPopup',
        data: {
            id: id,
            is_checked: is_checked
        }
    }).done(function (data) {
        
    });
}

