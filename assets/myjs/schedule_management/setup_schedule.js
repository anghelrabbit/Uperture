
$(function () {
});

$('span[name=btn_setup_sched]').click(function () {
    $('div[name=modal_scheduler]').modal('hide');
    $('div[name=modal_setup_schedule]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
//    $('#schedule_form input[type=date],#schedule_form input[type=time]').val('');
//    $('#schedule_form input[type=checkbox]').prop('checked', false);
//    $('label[name=days_sched_error],label[name=sched_datein_error],label[name=sched_dateout_error],label[name=sched_timein_error],label[name=sched_timeout_error]').empty();
});
$('button[name=btn_close_modal_setup_schedule]').click(function () {
    $('div[name=modal_setup_schedule]').modal('hide');
    $('div[name=modal_scheduler]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });

});
$('input[name=whole_week_box]').change(function () {
    if ($(this).is(':checked')) {
        $('div[name=dayweek_holder] input').prop('checked', true);
    } else {
        $('div[name=dayweek_holder] input').prop('checked', false);
    }
});

$('button[name=save_temp_schedule]').click(function () {
    organizeSchedule();
});


function organizeSchedule() {
    $.ajax({
        data: $('#schedule_form').serialize() + '&scheds=' + JSON.stringify(scheds) + '&sched_table=' + JSON.stringify(sched_table),
        url: 'ScheduleManagement/CheckSaveSchedule',
        dataType: 'json',
        type: 'POST'
    }).done(function (result) {
        var data = result['result'];
        if (data['success'] == false) {
            $.each(data['messages'], function (index, value) {
                $('label[name=' + index + '_error]').empty('');
                if (value != '') {
                    $('label[name=' + index + '_error]').removeClass('hidden');
                    $('label[name=' + index + '_error]').append(value);
                }
            });
        } else {
            scheds = result['data'];
            sched_table = result['sched_table'];
            console.log(scheds);
            schedTable();
        }
    });
}
function schedTable() {
    $('#setup_schedule_table').DataTable().clear().destroy();
    $('#setup_schedule_table').DataTable({
        dom: 'frtip',
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],
        ajax: {
            url: 'ScheduleManagement/SchedTable',
            type: 'POST',
            data: {
                sched_table: JSON.stringify(sched_table)
            },

        },

    });
    $('#setup_schedule_table_filter').empty();
    $('#setup_schedule_table_info').addClass('hidden');
    $('#setup_schedule_table_paginate').addClass('hidden');
    $('div[name=modal_setup_schedule]').modal('hide');
    $('div[name=modal_scheduler]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
}

function removeRowSched(sched_type, index, table_index) {
    $.ajax({
        data: {
            sched_type: sched_type,
            sched: index,
            scheds: JSON.stringify(scheds),
            table_index: table_index,
            sched_table: JSON.stringify(sched_table),
        },
        url: 'ScheduleManagement/RemoveSchedRow',
        dataType: 'json',
        type: 'POST'
    }).done(function (result) {
        scheds = result['sched'];
        sched_table = result['table'];
        schedTable();
    });
}
$('span[name=save_schedule]').click(function () {
    saveUpdateSchedule();
});
function saveUpdateSchedule() {
    $.ajax({
        data: {
            selected_department: JSON.stringify(selected_departments),
            unselected_department: JSON.stringify(unselected_departments),
            selectd_profileno: JSON.stringify(selected_profileno),
            unselectd_profileno: JSON.stringify(unselected_profileno),
            scheds: JSON.stringify(scheds),
        },
        url: 'ScheduleManagement/SaveUpdateSchedule',
        dataType: 'json',
        type: 'POST'
    }).done(function (result) {
        if (result == false) {

        } else {
            swal({title: "Success",
                text: "Schedule saved.",
                type: "success",
                show: true,
                backdrop: 'static',
                timer: 1600,
                showConfirmButton: false,
                keyboard: false},
                    function ()
                    {
                        $('div[name=modal_scheduler]').modal('hide');
                        swal.close();
                        organizeStruct('modal_struct_holder', 'page_struct_holder', 1);
                    });
        }
    });
}

function editSchedule(id, profileno, name) {
    select_profileno = profileno;
    sched_id = id;
    $('input[name=member_name]').val(name);
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'Employee/FetchEmployeeSched',
        data: {sched_id: sched_id}
    }).done(function (data) {
        $('div[name=modal_schedule]').modal('show');
        $('input[name=member_timein_date]').val(data['sched_datein']);
        $('input[name=member_timein_time]').val(data['sched_timein']);
        $('input[name=member_timeout_date]').val(data['sched_dateout']);
        $('input[name=member_timeout_time]').val(data['sched_timeout']);

    });

}

function removeSchedule(id) {
    swal({
        title: "Are you sure?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        confirmButtonText: "Yes Proceed.",
        closeOnConfirm: false
    }, function () {
        deleteSched(id);
        swal.close();
    });
}

function deleteSched(id) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'ScheduleManagement/RemoveSchedule',
        data: {id: id}
    }).done(function (data) {
        swal({title: "Success",
            text: "Employee Schedule removed.",
            type: "success",
            show: true,
            backdrop: 'static',
            timer: 1600,
            showConfirmButton: false,
            keyboard: false},
                function ()
                {
                    swal.close();
                    FetchEmployeeSched();
                });
    });

}

$('input[name=monthly_date]').change(function () {
    monthlyViewTable();
});
$('a[name=monthly_view_tab]').click(function () {
    monthlyViewTable();
});
function monthlyViewTable() {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'ScheduleManagement/FetchMonthlyEmpSched',
        data: {month_year: $('input[name=monthly_date]').val()}
    }).done(function (data) {
        console.log(data);
        $('#monthly_view_table').DataTable().clear().destroy();
        $('#monthly_view_table').DataTable({
            dom: 'frtip',
            responsive: true,
            processing: true,
            serverSide: true,
            order: [],
            ajax: {
                url: 'ScheduleManagement/FetchSchedulesMonthlyView',
                type: 'POST',
                data: {
                    month_year: $('input[name=monthly_date]').val(),
                    emp_sched: JSON.stringify(data)
                },

            },
           

        });
        $('#monthly_view_table_filter').empty();
        $('#monthly_view_table_info').addClass('hidden');
        $('#monthly_view_table_paginate').addClass('hidden');
    });
}


function showSchedClick(index) {
    $('span[name=show_sched_' + index + ']').addClass('hidden');
    $('span[name=hide_sched_' + index + ']').removeClass('hidden');
    $('textarea[name=textarea_sched_' + index + ']').removeClass('hidden');
}
function hideSchedClick(index) {
    $('span[name=show_sched_' + index + ']').removeClass('hidden');
    $('span[name=hide_sched_' + index + ']').addClass('hidden');
    $('textarea[name=textarea_sched_' + index + ']').addClass('hidden');
}