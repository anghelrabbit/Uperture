/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(function () {
    $('input[name=undertime_actualin]').val('12:00:00');

});



function fetchMyUndertime() {
    $('#my_undertime_table').DataTable().clear().destroy();
    $('#my_undertime_table').DataTable({
        dom: 'frtip',
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],
        "columnDefs": [
            {"visible": false, "targets": 0}
        ],
        ajax: {
            url: 'UnderTime/FetchMyUndertime',
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
    $('#my_undertime_table_filter').empty();
}

$('button[name=add_undertime]').on('click', function () {
    form = 1;
    $('#undertime_signatory').empty();
    $('#undertime_signatory').append(signatory_template);
    $('div[name=undertime_modal]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
     setupDiv(0);
    request_form_id = 0;
    for_cancellation = 0;
    has_schedule = false;
    var d = new Date();

    var month = d.getMonth() + 1;
    var day = d.getDate();

    var output = d.getFullYear() + '-' +
            (month < 10 ? '0' : '') + month + '-' +
            (day < 10 ? '0' : '') + day;
    $('input[name=undertime_worksched_datein]').val(output);
    refreshUndertimeForm();
    fetchSpecificWorkschedule($('input[name=undertime_worksched_datein]').val(), request_form_id, form);

});

function CheckSchedule(result) {

    $('div[name=undertime_worksched_error]').empty();
    if (result['has_schedule'] == false || result['has_undertime'] == true) {
        has_schedule = false;
        if (result['has_undertime'] == true) {
            swal({title: "Pending/Approved undertime on selected date.",
                type: "error",
                show: true,
                backdrop: 'static',
                timer: 3000,
                showConfirmButton: false,
                keyboard: false});
        } else {
            $('div[name=undertime_worksched_error').append('No available schedule');
        }


        $('input[name=undertime_datein_disable]').removeClass('hidden');
        $('input[name=undertime_actualin_disable]').removeClass('hidden');

        $('input[name=undertime_actual_datein]').addClass('hidden');
        $('input[name=undertime_actualin]').addClass('hidden');


        $('input[name=undertime_dateout_disable]').removeClass('hidden');
        $('input[name=undertime_actualout_disable]').removeClass('hidden');

        $('input[name=undertime_actual_dateout]').addClass('hidden');
        $('input[name=undertime_actualout]').addClass('hidden');
        $('button[name=save_undertime]').addClass('hidden');

    } else {
        has_schedule = true;
        $('input[name=undertime_worksched_dateout]').val(result['date_out']);
        $('input[name=undertime_worksched_timein]').val(result['time_in']);
        $('input[name=undertime_worksched_out]').val(result['time_out']);


        $('input[name=undertime_actualin]').val(result['time_in']);
        $('input[name=undertime_actual_datein]').val(result['date_in']);
        $('input[name=undertime_actualout]').val(result['time_out']);
        $('input[name=undertime_actual_dateout]').val(result['date_out']);
        $('button[name=save_undertime]').removeClass('hidden');

    }
    undertimeTypeRestriction();

}



function undertimeTypeRestriction() {
    if (has_schedule == true) {
        if ($('select[name=undertime_type]').val() == 1) {
            $('input[name=undertime_actual_datein]').val($('input[name=undertime_worksched_datein]').val());
            $('input[name=undertime_actualin]').val($('input[name=undertime_worksched_timein]').val());
            $('input[name=undertime_datein_disable]').val($('input[name=undertime_worksched_datein]').val());
            $('input[name=undertime_actualin_disable]').val($('input[name=undertime_worksched_timein]').val());

            $('input[name=undertime_datein_disable]').removeClass('hidden');
            $('input[name=undertime_actualin_disable]').removeClass('hidden');

            $('input[name=undertime_actual_datein]').addClass('hidden');
            $('input[name=undertime_actualin]').addClass('hidden');

            $('input[name=undertime_dateout_disable]').addClass('hidden');
            $('input[name=undertime_actualout_disable]').addClass('hidden');
            $('input[name=undertime_actual_dateout]').removeClass('hidden');
            $('input[name=undertime_actualout]').removeClass('hidden');
        } else {
            $('input[name=undertime_actual_dateout]').val($('input[name=undertime_worksched_dateout]').val());
            $('input[name=undertime_actualout]').val($('input[name=undertime_worksched_out]').val());
            $('input[name=undertime_dateout_disable]').val($('input[name=undertime_worksched_dateout]').val());
            $('input[name=undertime_actualout_disable]').val($('input[name=undertime_worksched_out]').val());


            $('input[name=undertime_dateout_disable]').removeClass('hidden');
            $('input[name=undertime_actualout_disable]').removeClass('hidden');

            $('input[name=undertime_actual_dateout]').addClass('hidden');
            $('input[name=undertime_actualout]').addClass('hidden');



            $('input[name=undertime_datein_disable]').addClass('hidden');
            $('input[name=undertime_actualin_disable]').addClass('hidden');
            $('input[name=undertime_actual_datein]').removeClass('hidden');
            $('input[name=undertime_actualin]').removeClass('hidden');
        }
    }
}

function saveUpdateUndertime() {

    $('div[name=loading_modal]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    $.ajax({
        type: 'POST',
        url: "Undertime/SaveUpdateUndertime",
        data: $('#undertime_form').serialize() + "&id=" + request_form_id,
        dataType: 'json'
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
                text: "Undertime saved.",
                type: "success",
                show: true,
                backdrop: 'static',
                timer: 1600,
                showConfirmButton: false,
                keyboard: false},
                    function ()
                    {
                        $('div[name=undertime_modal]').modal('hide');
                        swal.close();
                        tabCategory(1);
                    });
        }
    });
}

function fetchSelectedUndertime(id) {
    $('#undertime_signatory').empty();
    $('#undertime_signatory').append(signatory_template);
    $.ajax({

        type: 'POST',
        data: {
            id: id
        },
        url: 'Undertime/FetchSpecificUndertime',
        dataType: 'json'

    }).done(function (result) {
        $('div[name=undertime_modal]').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
        refreshUndertimeForm();
        userApproval(result, 0, 0, 0,0, 1);
        $('input[name=undertime_worksched_datein]').attr('readonly', false);

        $('input[name=undertime_employeename]').val(result['empname']);
        $('input[name=undertime_company]').val(result['company']);

        $('select[name=undertime_type]').val(result['undertime_type']);

        $('input[name=undertime_worksched_datein]').val(result['sched_datein']);

        $('input[name=undertime_worksched_dateout]').val(result['sched_dateout']);
        $('input[name=undertime_worksched_timein]').val(result['sched_timein']);
        $('input[name=undertime_worksched_out]').val(result['sched_timeout']);




        $('input[name=undertime_actual_datein]').val(result['actual_datein']);
        $('input[name=undertime_actual_dateout]').val(result['actual_dateout']);
        $('input[name=undertime_actualin]').val(result['actual_timein']);
        $('input[name=undertime_actualout]').val(result['actual_timeout']);

        has_schedule = true;
        request_form_id = result['id'];
        undertimeTypeRestriction();
        $('input[name=undertime_datein_disable]').val(result['actual_datein']);
        $('input[name=undertime_dateout_disable]').val(result['actual_dateout']);
        $('input[name=undertime_actualin_disable]').val(result['actual_timein']);
        $('input[name=undertime_actualout_disable]').val(result['actual_timeout']);
        $('textarea[name=undertime_reason]').val(result['reason']);
        $('div[name=undertime_worksched_error]').empty();

        $('a[href="#undertime_details"]').click();
        selectedFormButtons(result, 1);

    });
}







function refreshUndertimeForm() {
    $('div[name=supervisor_buttons]').empty();
    $('div[name=head_buttons]').empty();
    $('div[name=hr_buttons]').empty();

    $('div[name=supervisor_buttons]').append('Pending Approval');
    $('div[name=head_buttons]').append('Pending Approval');
    $('div[name=hr_buttons]').append('Pending Approval');

    $('input[name=undertime_worksched_dateout]').val('');
    $('input[name=undertime_worksched_timein]').val('');
    $('input[name=undertime_worksched_out]').val('');

    $('input[name=undertime_actualin]').val('');
    $('input[name=undertime_actual_datein]').val('');
    $('input[name=undertime_actualout]').val('');
    $('input[name=undertime_actual_dateout]').val('');


    $('input[name=undertime_datein_disable]').val('');
    $('input[name=undertime_actualin_disable]').val('');
    $('input[name=undertime_dateout_disable]').val('');
    $('input[name=undertime_actualout_disable]').val('');



    $('textarea[name=undertime_reason]').val('');
    $('textarea[name=undertime_reason]').attr('readonly', false);
    $('div[name=cancellation_content]').addClass('hidden');
    $('button[name=update_undertime]').addClass('hidden');
    $('button[name=remove_undertime]').addClass('hidden');
    $('button[name=cancel_undertime]').addClass('hidden');
    $('h4[name=undertime_title]').empty();
    $('h4[name=undertime_title]').append('Undertime Form');

    $('div[name=undertime_worksched_error]').empty();
    $('label[name=undertime_actual_datein_error]').empty();
    $('label[name=undertime_actualin_error]').empty();
    $('label[name=undertime_actual_dateout_error]').empty();
    $('label[name=undertime_actualout_error]').empty();
    $('select[name=undertime_type]').val(0);
}

$('button[name=save_undertime]').on('click', function () {
    saveUpdateUndertime();
});
$('button[name=update_undertime]').on('click', function () {
    saveUpdateUndertime();
});
$('button[name=remove_undertime]').on('click', function () {
    removeForm();
});
$('button[name=cancel_undertime]').on('click', function () {
    cancelForm();
});
$('input[name=undertime_worksched_datein]').on('change', function () {
    fetchSpecificWorkschedule($('input[name=undertime_worksched_datein]').val(), request_form_id, form);
});


