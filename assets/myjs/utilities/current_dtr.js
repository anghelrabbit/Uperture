/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(function () {
    refreshCurrentDTR();
    $.ajax({
        type: "POST",
        data: {
        },
        dataType: "json",
        url: "Overtime/SetupOvertimeTypes",
    }).done(function (result) {
        $('select[name=overtime_type_notif]').empty();
        $.each(result, function (key, value) {
            $('select[name=overtime_type_notif]').append('<option value="' + key + '">' + key + '</option>');
        });
        checkBreakIn();
    });
});




function refreshCurrentDTR() {
    $('#current_timein').val('Fetching');
    $('#current_timeout').val('Fetching');
    $('#refresh_icon').addClass('fa-spin');
    $("#current_timein").css({"background-color": "grey", "color": "black"});
    $("#current_timeout").css({"background-color": "grey", "color": "black"});
    $.ajax({
        type: 'POST',
        url: "DailyTimeRecord/RefreshCurrentDTR",
        dataType: 'json'
    }).done(function (data) {
        $('#refresh_icon').removeClass('fa-spin');
        $("#current_timein").css({"background-color": data[0][1][0], "color": data[0][1][1]});
        $("#current_timeout").css({"background-color": data[1][1][0], "color": data[1][1][1]});


        $('#current_timein').val(data[0][0]);
        $('#current_timeout').val(data[1][0]);
//                $('#current_timeout').val(data[1]);

    });
}

function empPunch(is_timein) {
    $.ajax({
        type: 'POST',
        url: "DailyTimeRecord/EmployeePunch",
        dataType: 'json',
        data: {is_timein: is_timein}
    }).done(function (data) {
        if (data['result']) {
            swal({title: "Success",
                text: "Punched successful.",
                type: "success",
                show: true,
                backdrop: 'static',
                timer: 1600,
                showConfirmButton: false,
                keyboard: false},
                    function ()
                    {

                        swal.close();
                        window.location.reload();

                    });
        } else {
            var addon_text = '';
            if (data['is_early'] == true) {
                swal({
                    title: "It looks like it's not your time for your work schedule, do you want this to log as overtime??",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Yes Proceed.",
                    closeOnConfirm: false
                }, function () {
                    $('div[name=modal_overtime_notif]').modal('show');
                    swal.close();
                });
            } else {

                if (data['has_biom'] == true) {
                    addon_text = 'Already punched.';
                } else if (data['has_sched'] == true) {
                    addon_text = 'No Schedule found.';
                }
                swal({title: "Error",
                    text: addon_text,
                    type: "error",
                    show: true,
                    backdrop: 'static',
                    timer: 1600,
                    showConfirmButton: false,
                    keyboard: false},
                        function ()
                        {
                            swal.close();
                        });
            }
        }
    });
}
$('input[name=overtime_worksched_datein_notif]').change(function () {
    $.ajax({
        type: 'POST',
        data: {date: $('input[name=overtime_worksched_datein_notif]').val(), id: 0, form: 4},
        url: 'RequestForms/FetchWorkschedule',
        dataType: 'json'

    }).done(function (result) {
        $('input[name=overtime_worksched_datein_notif]').val(result['date_in']);
        $('input[name=overtime_worksched_dateout_notif]').val(result['date_out']);
        $('input[name=overtime_worksched_timein_notif]').val(result['time_in']);
        $('input[name=overtime_worksched_out_notif]').val(result['time_out']);

    });
});

$('span[name=save_overtime_notif]').click(function () {
    saveUpdateOvertime();
});
function saveUpdateOvertime() {
    $('div[name=loading_modal]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    $.ajax({
        type: 'POST',
        url: "Overtime/SaveUpdateOvertimeNotif",
        data: $('#overtime_form_notif').serialize() + "&id=" + 0,
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
                        $('div[name=modal_overtime_notif]').modal('hide');
                        swal.close();
                    });
        }
    });
}
var is_break = 0;
var mins = 59;
var seconds = 59;
var break_timer = null;
var sched_reference = 0;
function checkBreakIn() {
    $.ajax({
        type: 'POST',
        url: "EmployeeBreak/FetchExistingBreak",
        dataType: 'json'
    }).done(function (result) {
        sched_reference = result;
        checkExistingBreak();
    });
}

function breakIn() {
    $.ajax({
        type: 'POST',
        url: "EmployeeBreak/breakIn",
        data: {
            timeleft: '00:' + mins + ":" + seconds
        },
        dataType: 'json'
    }).done(function (result) {
        if (result['has_schedule']) {
            if (result['has_punched']) {
                break_timer = setTimeout(function () {
                    breakTimer();
                }, 1000);
                $('a[name=take_break]').css('background-color', "#FF392E");
            } else {
                swal({title: "Error",
                    text: "You must punch-in.",
                    type: "error",
                    show: true,
                    backdrop: 'static',
                    timer: 1600,
                    showConfirmButton: false,
                    keyboard: false},
                        function ()
                        {
                            swal.close();
                        });
            }
        } else {
            swal({title: "Error",
                text: "No Schedule Found.",
                type: "error",
                show: true,
                backdrop: 'static',
                timer: 1600,
                showConfirmButton: false,
                keyboard: false},
                    function ()
                    {
                        swal.close();
                    });
        }
    });
}
function checkExistingBreak() {
    $.ajax({
        type: 'POST',
        url: "EmployeeBreak/HasBreakIn",
        dataType: 'json',
        data: {sched_ref: sched_reference}
    }).done(function (result) {
        console.log(result);
        mins = mins - result['mins'];
        seconds = seconds - result['secs'];
        if (result['has_break']) {
            breakClick(result['has_break']);
        }
    });
}
$('a[name=take_break]').click(function () {
    breakClick(false);
});

function breakClick(has_break) {
    if (is_break == 0) {
        is_break = 1;
        if (has_break == false) {
            fetchLastBreak();
        } else {
            break_timer = setTimeout(function () {
                breakTimer();
            }, 1000);
            $('a[name=take_break]').css('background-color', "#FF392E");
        }
    } else {
        breakOut();
        $('a[name=take_break]').css('background-color', "#3ED03E");
        is_break = 0;
        clearTimeout(break_timer);
    }
}

function breakTimer() {
    if (seconds != 0) {
        seconds--;
        break_timer = setTimeout(function () {
            breakTimer();
        }, 1000);
    } else {
        mins--;
        seconds = 59;
        if (mins == 0) {
            clearTimeout(break_timer);
        }
    }
    $('span[name=span_timer]').empty().append(mins + " : " + seconds);
}


function breakOut() {
    $.ajax({
        type: 'POST',
        url: "EmployeeBreak/breakOut",
        data: {
            timeleft: '00:' + mins + ":" + seconds,
            sched_ref: sched_reference
        },
        dataType: 'json'
    }).done(function (result) {
    });
}

function fetchLastBreak() {
    $.ajax({
        type: 'POST',
        url: "EmployeeBreak/FetchLastBreak",
        data: {
            sched_ref: sched_reference
        },
        dataType: 'json'
    }).done(function (result) {
        mins = result['mins'];
        seconds = result['secs'];
        breakIn();
    });
}