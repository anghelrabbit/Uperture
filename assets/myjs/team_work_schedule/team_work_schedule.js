var select_emp_table = null;
var select_profileno = '';
var sched_id = 0;
$(function () {
});







$('button[name=save_schedule_edit]').click(function () {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'Employee/SaveMemberSchedule',
        data: $('#form_member_schedule').serialize() + "&profileno=" + select_profileno + "&id=" + sched_id
    }).done(function (data) {
        swal({title: "Success",
            text: "Member Schedule saved.",
            type: "success",
            show: true,
            backdrop: 'static',
            timer: 1600,
            showConfirmButton: false,
            keyboard: false},
                function ()
                {
                    $('div[name=modal_schedule]').modal('hide');
                    swal.close();
                    FetchEmployeeSched();
                });

    });
});

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

function deleteSched(id){
     $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'ScheduleManagement/RemoveSchedule',
        data: {id: id}
    }).done(function (data) {
          swal({title: "Success",
            text: "Member Schedule removed.",
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