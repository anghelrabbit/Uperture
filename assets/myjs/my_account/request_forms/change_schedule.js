/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var date_category = 0;
var fromshift_dayoff = 0;
var toshift_dayoff = 0;
var shiftchange = 0;
var straightduty = 0;
var canceldayoff = 0;
var changedayoff = 0;

$(function () {
});

function fetchMyChangeSchedule() {
    $('#my_cs_table').DataTable().clear().destroy();
    $('#my_cs_table').DataTable({
        dom: 'frtip',
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],
        "columnDefs": [
            {"visible": false, "targets": 0}
        ],
        ajax: {
            url: 'ChangeSchedule/FetchMyChangeSchedule',
            type: 'POST',
            data: {
                datein: $('#worksched_in').val(),
                dateout: $('#worksched_out').val()
            },

        },
        createdRow: function (row, data, dataIndex)
        {
            if (data[0] > 0) {
                $(row).css('background-color', '#ff8785');
            }
        },
        drawCallback: function (settings, json) {
            $('div[name=loading_overlay]').addClass('hidden');
        }

    });
    $('#my_cs_table_filter').empty();
}

function fetchSelectedCS(id) {
    refreshCSForm();
    $('#cs_signatory').empty();
    $('#cs_signatory').append(signatory_template);
    form = 2;
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'ChangeSchedule/FetchSpecificCS',
        data: {id: id}
    }).done(function (data) {
        var result = data['info'];
        request_form_id = id;
        $('div[name=modal_cs]').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
        $("#shiftchange").prop("checked", false);
        $("#straightduty").prop("checked", false);
        $("#canceldayoff").prop("checked", false);
        $("#changedayoff").prop("checked", false);
        $('a[href="#cs_details"]').click();
        $('input[name=cs_date_filed]').val(result['date_requested']);
        $('textarea[name=cs_reason]').val(result['reason']);

        if (result['shiftchange'] == 1) {
            $("input[name=shiftchange]").prop("checked", true);
            shiftchange = 1;
        }
        if (result['straightduty'] == 1) {
            $("input[name=straightduty]").prop("checked", true);
            straightduty = 1;
        }
        if (result['canceldayoff'] == 1) {
            $("input[name=canceldayoff]").prop("checked", true);
            canceldayoff = 1;
        }
        if (result['changedayoff'] == 1) {
            $("input[name=changedayoff]").prop("checked", true);
            changedayoff = 1;
        }
  $('input[name=cs_relievername]').val(result['reliever_name']);
        if ($('input[name=canceldayoff]').is(':checked')) {
            if ($('input[name=cs_fromshift_datein]').val() != '') {
                $('input[name=cs_toshift_datein]').val($('input[name=cs_fromshift_datein]').val());
                $('input[name=cs_toshift_dateout]').val($('input[name=cs_fromshift_datein]').val());
                $('input[name=cs_toshift_timein]').val('');
                $('input[name=cs_toshift_timeout]').val('');
            }
            $('input[name=cs_toshift_datein]').attr('readonly', true);
        } else {
            $('input[name=cs_toshift_datein]').attr('readonly', false);
        }
        date_category = 0;
        checkSelectedSchedule(data['worksched']);
        date_category = 1;
        checkSelectedSchedule(data['toshift']);
        selectedFormButtons(result, 2);
        userApproval(result, 0, 0, 0, 0,2);


    });
}






function checkSelectedSchedule(data) {
    var shift = '';
    if (date_category == 0) {
        shift = 'fromshift';
        fromshift_dayoff = 0;
    } else {
        shift = 'toshift';
        toshift_dayoff = 0;
    }
    $('input[name=cs_' + shift + '_timein]').removeClass('hidden');
    $('input[name=cs_' + shift + '_timein_dayoff]').addClass('hidden');
    $('input[name=cs_' + shift + '_timeout]').removeClass('hidden');
    $('input[name=cs_' + shift + '_timeout_dayoff]').addClass('hidden');
    if (data['time_in'] == 'Day Off') {
        $('input[name=cs_' + shift + '_timein]').addClass('hidden');
        $('input[name=cs_' + shift + '_timein_dayoff]').removeClass('hidden');
        $('input[name=cs_' + shift + '_timein_dayoff]').val('Day Off');
        $('input[name=cs_' + shift + '_timein]').val('');

        $('input[name=cs_' + shift + '_timeout]').addClass('hidden');
        $('input[name=cs_' + shift + '_timeout_dayoff]').removeClass('hidden');
        $('input[name=cs_' + shift + '_timeout_dayoff]').val('Day Off');
        $('input[name=cs_' + shift + '_timeout]').val('');

        if (date_category == 0) {
            fromshift_dayoff = 1;
        } else {
            toshift_dayoff = 1;
        }
    } else {
        $('input[name=cs_' + shift + '_timein]').val(data['time_in']);
        $('input[name=cs_' + shift + '_timein_dayoff]').val('Not Day Off');

        $('input[name=cs_' + shift + '_timeout]').val(data['time_out']);
        $('input[name=cs_' + shift + '_timeout_dayoff]').val('Not Day Off');
    }
    
    $('input[name=cs_' + shift + '_datein]').val(data['date_in']);
    $('input[name=cs_' + shift + '_dateout]').val(data['date_out']);
    $('input[name=cs_toshift_datein]').val(data['date_in']);
    $('input[name=cs_toshift_dateout]').val(data['date_out']);
}

function saveUpdateChangeSchedule() {
    $('div[name=loading_modal]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'ChangeSchedule/SaveUpdateChangeSchedule',
        data: {
            id: request_form_id,
            reliever: reliever_prof,

            fromshift_is_dayoff: fromshift_dayoff,
            toshift_is_dayoff: toshift_dayoff,

            shiftchange: shiftchange,
            straightduty: straightduty,
            canceldayoff: canceldayoff,
            changedayoff: changedayoff,

            cs_fromshift_datein: $('input[name=cs_fromshift_datein]').val(),
            cs_fromshift_dateout: $('input[name=cs_fromshift_dateout]').val(),
            cs_fromshift_timein: $('input[name=cs_fromshift_timein]').val(),
            cs_fromshift_timeout: $('input[name=cs_fromshift_timeout]').val(),

            cs_toshift_datein: $('input[name=cs_toshift_datein]').val(),
            cs_toshift_dateout: $('input[name=cs_toshift_dateout]').val(),
            cs_toshift_timein: $('input[name=cs_toshift_timein]').val(),
            cs_toshift_timeout: $('input[name=cs_toshift_timeout]').val(),
            
            reason: $('textarea[name=cs_reason]').val()

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
                text: "Change Schedule saved.",
                type: "success",
                show: true,
                backdrop: 'static',
                timer: 1600,
                showConfirmButton: false,
                keyboard: false},
                    function ()
                    {
                        $('div[name=modal_cs]').modal('hide');
                        swal.close();
                        tabCategory(2);
                    });
        }
    });
}




function refreshCSForm() {
    $('a[href="#cs_details"]').click();
    $('div[name=supervisor_buttons]').empty();
    $('div[name=head_buttons]').empty();
    $('div[name=hr_buttons]').empty();
    $('div[name=reliever_buttons]').empty();

    $('div[name=reliever_buttons]').append('Pending Approval');
    $('div[name=supervisor_buttons]').append('Pending Approval');
    $('div[name=head_buttons]').append('Pending Approval');
    $('div[name=hr_buttons]').append('Pending Approval');
    $('div[name=cancellation_content]').addClass('hidden');

    $('button[name=save_cs]').removeClass('hidden');
    $('button[name=update_cs]').addClass('hidden');
    $('button[name=remove_cs]').addClass('hidden');
    $('button[name=cancel_cs]').addClass('hidden');
    $('h4[name=cs_title]').empty();
    $('h4[name=cs_title]').append('Change Schedule Form');

    shiftchange = 0;
    straightduty = 0;
    canceldayoff = 0;
    changedayoff = 0;
    $('input[name=cs_fromshift_datein]').val('');
    $('input[name=cs_fromshift_dateout]').val('');
    $('input[name=cs_fromshift_timein]').val('');
    $('input[name=cs_fromshift_timeout]').val('');

    $('input[name=cs_fromshift_timein]').removeClass('hidden');
    $('input[name=cs_fromshift_timeout]').removeClass('hidden');
    $('input[name=cs_fromshift_timein_dayoff]').addClass('hidden');
    $('input[name=cs_fromshift_timeout_dayoff]').addClass('hidden');

    $('input[name=cs_fromshift_timein_dayoff]').val('');
    $('input[name=cs_fromshift_timeout_dayoff]').val('');

    $('input[name=cs_toshift_datein]').val('');
    $('input[name=cs_toshift_dateout]').val('');
    $('input[name=cs_toshift_timein]').val('');
    $('input[name=cs_toshift_timeout]').val('');

    $('input[name=cs_toshift_timein]').removeClass('hidden');
    $('input[name=cs_toshift_timeout]').removeClass('hidden');
    $('input[name=cs_toshift_timein_dayoff]').addClass('hidden');
    $('input[name=cs_toshift_timeout_dayoff]').addClass('hidden');

    $('input[name=cs_toshift_timein_dayoff]').val('');
    $('input[name=cs_toshift_timeout_dayoff]').val('');

    $('textarea[name=cs_reason]').val('');

    $('label[name=cs_fromshift_datein_error]').empty();
    $('label[name=cs_fromshift_dateout_error]').empty();
    $('label[name=cs_fromshift_timein_error]').empty();
    $('label[name=cs_fromshift_timeout_error]').empty();

    $('label[name=cs_toshift_datein_error]').empty();
    $('label[name=cs_toshift_dateout_error]').empty();
    $('label[name=cs_toshift_timein_error]').empty();
    $('label[name=cs_toshift_timeout_error]').empty();

    $('label[name=cs_category_error]').empty();
    $('input[name=shiftchange]').prop('checked', false);
    $('input[name=straightduty]').prop('checked', false);
    $('input[name=canceldayoff]').prop('checked', false);
    $('input[name=changedayoff]').prop('checked', false);


}
$('button[name=add_cs]').click(function () {
    form = 2;
    $('#cs_signatory').empty();
    $('#cs_signatory').append(signatory_template);
    $('div[name=modal_cs]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    request_form_id = 0;
    for_cancellation = 0;
    refreshCSForm();
     setupDiv(1);
});

$('button[name=save_cs]').click(function () {
    saveUpdateChangeSchedule();
});
$('button[name=update_cs]').click(function () {
    saveUpdateChangeSchedule();
});

$('button[name=remove_cs]').click(function () {
    removeForm();
});
$('button[name=cancel_cs]').click(function () {
    cancelForm();
});

$('input[name=shiftchange]').click(function () {
    shiftchange = ($('input[name=shiftchange]').is(':checked')) ? 1 : 0;
});
$('input[name=straightduty]').click(function () {
    straightduty = ($('input[name=straightduty]').is(':checked')) ? 1 : 0;
});
$('input[name=canceldayoff]').click(function () {
    canceldayoff = ($('input[name=canceldayoff]').is(':checked')) ? 1 : 0;
});
$('input[name=changedayoff]').click(function () {
    changedayoff = ($('input[name=changedayoff]').is(':checked')) ? 1 : 0;
});


$('input[name=cs_fromshift_datein]').change(function () {
    fetchSpecificWorkschedule($('input[name=cs_fromshift_datein]').val(), request_form_id, form);
    date_category = 0;
});
$('input[name=cs_toshift_datein]').change(function () {
    fetchSpecificWorkschedule($('input[name=cs_toshift_datein]').val(), request_form_id, form);
    date_category = 1;
});

$('input[name=cs_toshift_timein_dayoff]').hover(function () {
    if ($('input[name=cs_toshift_timein_dayoff]').val() == 'Day Off') {
        $('input[name=cs_toshift_timein]').removeClass('hidden');
        $('input[name=cs_toshift_timein_dayoff]').addClass('hidden');
    }
});
$('input[name=cs_toshift_timeout_dayoff]').hover(function () {
    if ($('input[name=cs_toshift_timeout_dayoff]').val() == 'Day Off') {
        $('input[name=cs_toshift_timeout]').removeClass('hidden');
        $('input[name=cs_toshift_timeout_dayoff]').addClass('hidden');
    }
});

$('input[name=cs_toshift_timein]').mouseout(function () {
    if ($('input[name=cs_toshift_timein]').val() == '' && $('input[name=cs_toshift_timein_dayoff]').val() == 'Day Off') {
        $('input[name=cs_toshift_timein]').addClass('hidden');
        $('input[name=cs_toshift_timein_dayoff]').removeClass('hidden');
    } else {
        $('input[name=cs_toshift_timein]').removeClass('hidden');
        $('input[name=cs_toshift_timein_dayoff]').addClass('hidden');

    }
});
$('input[name=cs_toshift_timeout]').mouseout(function () {
    if ($('input[name=cs_toshift_timeout]').val() == '' && $('input[name=cs_toshift_timeout_dayoff]').val() == 'Day Off') {
        $('input[name=cs_toshift_timeout]').addClass('hidden');
        $('input[name=cs_toshift_timeout_dayoff]').removeClass('hidden');
    } else {
        $('input[name=cs_toshift_timeout]').removeClass('hidden');
        $('input[name=cs_toshift_timeout_dayoff]').addClass('hidden');

    }
});


$('span[name=reliever_btn]').click(function () {
    $('div[name=modal_reliever]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    setupRelievers();
});

$('input[name=canceldayoff]').change(function () {
    if ($('input[name=canceldayoff]').is(':checked')) {
        if ($('input[name=cs_fromshift_datein]').val() != '') {
            $('input[name=cs_toshift_datein]').val($('input[name=cs_fromshift_datein]').val());
            $('input[name=cs_toshift_dateout]').val($('input[name=cs_fromshift_datein]').val());
            $('input[name=cs_toshift_timein]').val('');
            $('input[name=cs_toshift_timeout]').val('');
        }
        $('input[name=cs_toshift_datein]').attr('readonly', true);
    } else {
        $('input[name=cs_toshift_datein]').attr('readonly', false);
    }

});