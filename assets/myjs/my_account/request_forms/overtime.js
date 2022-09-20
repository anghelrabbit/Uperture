/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var schedule = {};
$(function () {
    $.ajax({
        type: "POST",
        data: {
        },
        dataType: "json",
        url: "Overtime/SetupOvertimeTypes",
    }).done(function (result) {
        $('select[name=overtime_type]').empty();
        $.each(result, function (key, value) {
            $('select[name=overtime_type]').append('<option value="' + key + '">' + key + '</option>');
        });
    });
});



function CheckOvertimeSchedule(result) {
    schedule = result;
    changeOvertimeType();
}

function fetchMyOvertime() {
    $('#my_overtime_table').DataTable().clear().destroy();
    $('#my_overtime_table').DataTable({
        dom: 'frtip',
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],
        "columnDefs": [
            {"visible": false, "targets": 0}
        ],
        ajax: {
            url: 'Overtime/FetchMyOvertime',
            type: 'POST',
            data: {
                datein: $('#worksched_in').val(),
                dateout: $('#worksched_out').val()
            },

        },
        createdRow: function (row, data, dataIndex)
        {
            if (data[0] > 0 && data != null) {
                $(row).css('background-color', '#ff8785');
            }
        },
          drawCallback: function (settings, json) {
               $('div[name=loading_overlay]').addClass('hidden');
        }

    });
    $('#my_overtime_table_filter').empty();
}



$('button[name=add_overtime]').click(function () {
    $('div[name=modal_overtime]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    $('a[name=overtime_details]').click();
    request_form_id = 0;
    var d = new Date();

    var month = d.getMonth() + 1;
    var day = d.getDate();
    var output = d.getFullYear() + '-' +
            (month < 10 ? '0' : '') + month + '-' +
            (day < 10 ? '0' : '') + day;
    $('input[name=overtime_worksched_datein]').val(output);
    refreshOvertimeForm();
    fetchSpecificWorkschedule($('input[name=overtime_worksched_datein]').val(), request_form_id, form);

 setupDiv(0);

    $('div[name=supervisor_buttons]').append('Pending Approval');
    $('div[name=head_buttons]').append('Pending Approval');
    $('div[name=hr_buttons]').append('Pending Approval');

    $('h4[name=overtime_title]').empty();
    $('h4[name=overtime_title]').append('Overtime Form');


});


function saveUpdateOvertime() {
    $('label[name=overtime_actual_datein_error]').empty();
    $('label[name=overtime_actualin_error]').empty();
    $('label[name=overtime_actual_dateout_error]').empty();
    $('label[name=overtime_actualout_error]').empty();
    $('div[name=loading_modal]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    $.ajax({
        type: 'POST',
        url: "Overtime/SaveUpdateOvertime",
        data: $('#overtime_form').serialize() + "&id=" + request_form_id,
        dataType: 'json'
    }).done(function (data) {
        $('div[name=loading_modal]').modal('hide');
        if (data['success'] == false) {
            $.each(data['messages'], function (index, value) {
                if (value != '') {
                    $('label[name=' + index + '_error]').removeClass('hidden');
                    $('label[name=' + index + '_error]').empty('');
                    $('label[name=' + index + '_error]').append(value);
                }
            });
        } else {
            swal({title: "Success",
                text: "Overtime saved.",
                type: "success",
                show: true,
                backdrop: 'static',
                timer: 1600,
                showConfirmButton: false,
                keyboard: false},
                    function ()
                    {
                        $('div[name=modal_overtime]').modal('hide');
                        tabCategory(3);
                        swal.close();
                    });
        }
    });
}
function fetchSelectedOvertime(id) {
    refreshOvertimeForm();
    request_form_id = id;
    $.ajax({
        type: 'POST',
        url: "Overtime/FetchSpecificOvertime",
        data: {id: id},
        dataType: 'json'
    }).done(function (result) {
        $('div[name=modal_overtime]').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
        userApproval(result, 0, 0, 0, 1);
        $('input[name=overtime_worksched_datein]').attr('readonly', false);

        $('input[name=overtime_employeename]').val(result['empname']);
        $('input[name=overtime_company]').val(result['company']);
        $('select[name=overtime_type]').val(result['ot_refno']);
        $('input[name=overtime_worksched_datein]').val(result['workshed_datein']);
        $('input[name=overtime_worksched_dateout]').val(result['worksched_dateout']);
        $('input[name=overtime_worksched_timein]').val(result['workshed_timein']);
        $('input[name=overtime_worksched_out]').val(result['worksched_timeout']);

        $('input[name=overtime_actual_datein]').val(result['actual_datein']);
        $('input[name=overtime_actual_dateout]').val(result['actual_dateout']);
        $('input[name=overtime_actualin]').val(result['actual_timein']);
        $('input[name=overtime_actualout]').val(result['actual_timeout']);
        if (result['ot_refno'] == '' || result['ot_refno'] == null) {
            fetchSpecificWorkschedule(result['workshed_datein'], request_form_id, form);
        }
        $('input[name=overtime_reason]').val(result['ot_reason']);

        if ($('select[name=overtime_type]').val() == 'REST DAY OVERTIME') {
            $('input[name=overtime_excess_from]').val(result['excess_rdot_timein']);
            $('input[name=overtime_excess_to]').val(result['excess_rdot_timeout']);
            $('input[name=overtime_excess_total]').val(result['total_excess']);
            $('div[name=excess_rdot]').removeClass('hidden');
        } else {
            $('div[name=excess_rdot]').addClass('hidden');
        }


        $('a[href="#overtime_details"]').click();
        selectedFormButtons(result, 3);




    });
}


function CheckDateTime(category) {
    var worksched_date = '';
    var worksched_time = '';
    var actual_date = '';
    var actual_time = '';
    if ($('select[name=overtime_type]').val() == 'BEFORE SHIFT OVERTIME') {
        worksched_date = $('input[name=overtime_worksched_datein]').val();
        worksched_time = $('input[name=overtime_worksched_timein]').val();
    } else if ($('select[name=overtime_type]').val() == 'AFTER SHIFT OVERTIME') {
        worksched_date = $('input[name=overtime_worksched_dateout]').val();
        worksched_time = $('input[name=overtime_worksched_out]').val();
    } else if ($('select[name=overtime_type]').val() == 'REST DAY OVERTIME') {
        worksched_date = $('input[name=overtime_actual_datein]').val();
        worksched_time = $('input[name=overtime_actualin]').val();
        actual_date = $('input[name=overtime_actual_dateout]').val();
        actual_time = $('input[name=overtime_actualout]').val();
    }
    if ($('select[name=overtime_type]').val() != 'REST DAY OVERTIME') {
        if (category == 0) {
            actual_date = $('input[name=overtime_actual_datein]').val();
            actual_time = $('input[name=overtime_actualin]').val();
        } else {
            actual_date = $('input[name=overtime_actual_dateout]').val();
            actual_time = $('input[name=overtime_actualout]').val();
        }
    }
    $.ajax({
        type: "POST",
        data: {
            overtime_type: $('select[name=overtime_type]').val(),
            worksched_date: worksched_date,
            worksched_time: worksched_time,
            actual_date: actual_date,
            actual_time: actual_time
        },
        dataType: "json",
        url: "Overtime/OvertimeTypeRestriction",
    }).done(function (result) {
        if (result.length == 0) {
            $('input[name=overtime_excess_from]').val('');
            $('input[name=overtime_excess_to]').val('');
            $('input[name=overtime_excess_total]').val('');
        }
        if ($('select[name=overtime_type]').val() != 'REST DAY OVERTIME') {
            if (category == 0) {
                if ('actual_time' in result == true) {
                    $('input[name=overtime_actual_datein]').val(result['actual_date']);
                    $('input[name=overtime_actualin]').val(result['actual_time']);
                } else {
                    $('input[name=overtime_actual_datein]').val(result['actual_date']);

                }
            } else {
                if ('actual_time' in result == true) {
                    $('input[name=overtime_actual_dateout]').val(result['actual_date']);
                    $('input[name=overtime_actualout]').val(result['actual_time']);
                } else {
                    $('input[name=overtime_actual_dateout]').val(result['actual_date']);

                }

            }
        } else {
            if ('rdot_start_date' in result) {
                $('input[name=overtime_excess_from]').val(result['rdot_start_date'] + " " + result['rdot_start_time']);
                $('input[name=overtime_excess_to]').val(result['rdot_end_date'] + " " + result['rdot_end_time']);
                $('input[name=overtime_excess_total]').val(result['total_excess'] + " hour/s");
            }
        }
    });

}


function changeOvertimeType() {
    var has_error = false;
    if (request_form_id == 0) {
        $('button[name=save_overtime]').removeClass('hidden');
    }

    $('input[name=overtime_excess_from]').val('');
    $('input[name=overtime_excess_to]').val('');
    $('input[name=overtime_excess_total]').val('');
    $('div[name=overtime_worksched_error]').empty();
    $('div[name=excess_rdot]').addClass('hidden');
    $('input[name=overtime_actual_datein]').attr('readonly', false);
    $('input[name=overtime_actualout]').attr('readonly', false);
    $('input[name=overtime_actual_dateout]').attr('readonly', false);
    $('input[name=overtime_actualin]').attr('readonly', false);
    if (($('select[name=overtime_type]').val() == 'BEFORE SHIFT OVERTIME' || $('select[name=overtime_type]').val() == 'AFTER SHIFT OVERTIME')) {
        if (schedule != null && schedule['has_schedule'] == true) {

            $('input[name=overtime_worksched_dateout]').val(schedule['date_out']);
            $('input[name=overtime_worksched_timein]').val(convert12HourFormat(schedule['time_in']));
            $('input[name=overtime_worksched_out]').val(convert12HourFormat(schedule['time_out']));


            if ($('select[name=overtime_type]').val() == 'BEFORE SHIFT OVERTIME') {
                $('input[name=overtime_actual_datein]').val(schedule['date_in']);
                $('input[name=overtime_actualout]').val(schedule['time_in']);
                $('input[name=overtime_actual_dateout]').val(schedule['date_in']);
                $('input[name=overtime_actualin]').val('');
            } else {
                $('input[name=overtime_actual_datein]').val(schedule['date_out']);
                $('input[name=overtime_actualin]').val(schedule['time_out']);
                $('input[name=overtime_actual_dateout]').val(schedule['date_out']);
                $('input[name=overtime_actualout]').val('');
            }
        } else {
            $('div[name=overtime_worksched_error]').append('No Available Schedule');
            $('input[name=overtime_actual_datein]').attr('readonly', true);
            $('input[name=overtime_actualout]').attr('readonly', true);
            $('input[name=overtime_actual_dateout]').attr('readonly', true);
            $('input[name=overtime_actualin]').attr('readonly', true);
            has_error = true;
        }
    }
    if ($('select[name=overtime_type]').val() == 'REST DAY OVERTIME') {
        if (schedule['has_schedule'] == false) {
            $('div[name=excess_rdot]').removeClass('hidden');
            $('input[name=overtime_worksched_dateout]').val(schedule['date_in']);
            $('input[name=overtime_worksched_timein]').val(schedule['time_in']);
            $('input[name=overtime_worksched_out]').val(schedule['time_out']);
            $('input[name=overtime_actual_datein]').val(schedule['date_in']);
            $('input[name=overtime_actual_dateout]').val(schedule['date_in']);
            $('div[name=overtime_worksched_error]').empty();
        } else {
            $('div[name=overtime_worksched_error]').append('Selected date is not Day-off');
            $('input[name=overtime_actual_datein]').attr('readonly', true);
            $('input[name=overtime_actualout]').attr('readonly', true);
            $('input[name=overtime_actual_dateout]').attr('readonly', true);
            $('input[name=overtime_actualin]').attr('readonly', true);
            has_error = true;
        }

    }

    if ($('select[name=overtime_type]').val() == 'ONCALL') {
        $('input[name=overtime_actual_datein]').val('');
        $('input[name=overtime_actualout]').val('');
        $('input[name=overtime_actual_dateout]').val('');
        $('input[name=overtime_actualin]').val('');
    }

    if (has_error == true) {
        $('input[name=overtime_worksched_dateout]').val('');
        $('input[name=overtime_worksched_timein]').val('');
        $('input[name=overtime_worksched_out]').val('');
        $('input[name=overtime_actual_datein]').val('');
        $('input[name=overtime_actual_dateout]').val('');
        $('input[name=overtime_actualin]').val('');
        $('input[name=overtime_actualout]').val('');
        $('button[name=save_overtime]').addClass('hidden');
    }
}


function refreshOvertimeForm() {
    form = 3;
    $('#overtime_signatory').empty();
    $('#overtime_signatory').append(signatory_template);
    $('div[name=cancellation_content]').addClass('hidden');

    $('input[name=overtime_worksched_timein]').val('');
    $('input[name=overtime_worksched_dateout]').val('');
    $('input[name=overtime_worksched_out]').val('');

    $('input[name=overtime_actual_datein]').val('');
    $('input[name=overtime_actualin]').val('');
    $('input[name=overtime_actual_dateout]').val('');
    $('input[name=overtime_actualout]').val('');

    $('textarea[name=overtime_reason]').val('');

    $('input[name=overtime_actual_datein]').attr('readonly', false);
    $('input[name=overtime_actualin]').attr('readonly', false);
    $('input[name=overtime_actual_dateout]').attr('readonly', false);
    $('input[name=overtime_actualout]').attr('readonly', false);
    $('textarea[name=overtime_reason]').attr('readonly', false);


    $('input[name=overtime_excess_from]').val('');
    $('input[name=overtime_excess_to]').val('');
    $('input[name=overtime_excess_total]').val('');
    $('div[name=excess_rdot]').addClass('hidden');

    $('div[name=overtime_worksched_error]').empty();
    $('label[name=overtime_actual_datein_error]').empty();
    $('label[name=overtime_actualin_error]').empty();
    $('label[name=overtime_actual_dateout_error]').empty();
    $('label[name=overtime_actualout_error]').empty();


    $('div[name=cancellation_content]').addClass('hidden');
    $('button[name=update_overtime]').addClass('hidden');
    $('button[name=remove_overtime]').addClass('hidden');
    $('button[name=cancel_overtime]').addClass('hidden');

}

$('select[name=overtime_type]').change(function () {
    changeOvertimeType();
});
$('input[name=overtime_worksched_datein]').change(function () {
    fetchSpecificWorkschedule($('input[name=overtime_worksched_datein]').val(), request_form_id, form);
});

$('input[name=overtime_actual_datein]').change(function () {
    CheckDateTime(0);
});
$('input[name=overtime_actualin]').change(function () {
    CheckDateTime(0);
});
$('input[name=overtime_actual_dateout]').change(function () {
    CheckDateTime(1);
});
$('input[name=overtime_actualout]').change(function () {
    CheckDateTime(1);
});


$('button[name=save_overtime]').click(function () {
    saveUpdateOvertime();
});
$('button[name=update_overtime]').click(function () {
    saveUpdateOvertime();
});
$('button[name=remove_overtime]').click(function () {
    removeForm();
});
$('button[name=cancel_overtime]').click(function () {
    cancelForm();
});
