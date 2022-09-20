/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var id = 0;
var leave_name = '';
var leavetype_count = {};
function leaveTypeTable(form_category, profileno) {
    $('#leavetype_table').DataTable().clear().destroy();
    $('#leavetype_table').DataTable({
        columnDefs: [{
                targets: [0, 1, 2],
                orderable: false,
            }],
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "LeaveCredits/FetchLeaveTypes",
            type: "POST",
            data: {category: 0, from_leavetype: form_category, profileno: profileno}
        },
    });
    $('#leavetype_table_length').addClass('hidden');
    $('#leavetype_table_filter').addClass('hidden');
    $('#leavetype_table_info').addClass('hidden');
}



$("div[name=modal_givecredits]").on("hidden.bs.modal", function () {
    $('div[name=modal_struct_holder]').empty();
    $('div[name=struct_holder]').append(structure_template);
    FetchRole().done(function () {
        tabCategory(0);
    });
});




function saveLeaveType() {
    $.ajax({
        type: 'POST',
        url: 'LeaveCredits/SaveUpdateLeaveType',
        data: {id: id,
            leavetype_name: leave_name
        },
        dataType: 'json'
    }).done(function (result) {
        if (result['success'] === false) {
            $.each(result['messages'], function (index, value) {
                if (value != '') {
                    $('label[name=' + index + '_error]').empty('');
                    $('label[name=' + index + '_error]').append(value);
                }
            });
        } else {
            swal({title: "Success",
                text: "Leave Type saved.",
                type: "success",
                show: true,
                backdrop: 'static',
                timer: 1600,
                showConfirmButton: false,
                keyboard: false},
                    function ()
                    {
                        fetchLeaveTypes();
                        leaveTypeTable(1, '');
                        swal.close();
                    });

        }
    });
}

function removeLeaveType() {
    $.ajax({
        type: 'POST',
        url: 'LeaveCredits/RemoveSpecificLeaveType',
        data: {id: id,
        },
        dataType: 'json'
    }).done(function (result) {
        swal({title: "Success",
            text: "Leave Type removed.",
            type: "success",
            show: true,
            backdrop: 'static',
            timer: 1600,
            showConfirmButton: false,
            keyboard: false},
                function ()
                {
                    fetchLeaveTypes();
//                    addLeaveTypeTable();
                    swal.close();
                });

    });
}

function updateLeaveCredits() {
    $('#Searching_Modal').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    $.ajax({
        type: 'POST',
        url: 'LeaveCredits/UpdateEmployeeLeaveCredits',
        data: {
            selected_departments: JSON.stringify(selected_departments),
            unselected_departments: JSON.stringify(unselected_departments),
            selectd_profileno: JSON.stringify(selected_profileno),
            unselectd_profileno: JSON.stringify(unselected_profileno),
            leavetypes: JSON.stringify(leavetype_count)
        },
        dataType: 'json'
    }).done(function (result) {
        if (result) {
            $('#Searching_Modal').modal('hide');
            swal({title: "Success",
                text: "Leave credits saved.",
                type: "success",
                show: true,
                backdrop: 'static',
                timer: 1200,
                showConfirmButton: false,
                keyboard: false},
                    function ()
                    {
                        swal.close();
                        $('div[name=modal_givecredits]').modal('hide');
                        location.reload();
                    });
        }
    });
}

function leaveTypeCount(leavetype, input) {
    leavetype_count[leavetype] = $(input).val();
}

function updateLeaveType(index, option) {
    if (option == 1) {
        $('input[name=' + index + '_input]').removeClass('hidden');
        $('span[name=' + index + '_update]').removeClass('hidden');
        $('span[name=' + index + '_cancel]').removeClass('hidden');
        $('span[name=' + index + '_text]').addClass('hidden');
        $('span[name=' + index + '_edit]').addClass('hidden');
        $('span[name=' + index + '_delete]').addClass('hidden');
    } else if (option == 4) {
        $('input[name=' + index + '_input]').addClass('hidden');
        $('span[name=' + index + '_update]').addClass('hidden');
        $('span[name=' + index + '_cancel]').addClass('hidden');
        $('span[name=' + index + '_text]').removeClass('hidden');
        $('span[name=' + index + '_edit]').removeClass('hidden');
        $('span[name=' + index + '_delete]').removeClass('hidden');
        $('label[name=' + index + '_error]').empty();
    } else if (option == 3) {
        if ($('input[name=' + index + '_input]').val() == '') {
            $('label[name=' + index + '_error]').empty();
            $('label[name=' + index + '_error]').append('Leave type name required.');
        } else {
            id = index;
            leave_name = $('input[name=' + index + '_input]').val();
            saveLeaveType();
        }
    } else {
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-success",
            confirmButtonText: "Yes Proceed.",
            closeOnConfirm: false
        }, function () {
            id = index;
            removeLeaveType();
        });
    }
}



function updateEmployeeCreditCount(profileno, index) {
    $.ajax({
        type: 'POST',
        url: 'LeaveCredits/UpdateEmployeeCredit',
        data: {
            credit_count: $('input[name=' + index + '_credit]').val(),
            profileno: profileno,
            leavetype: index
        },
        dataType: 'json'
    }).done(function (result) {
        if (result) {
            swal({title: "Success",
                text: "Employee credits saved.",
                type: "success",
                show: true,
                backdrop: 'static',
                timer: 1200,
                showConfirmButton: false,
                keyboard: false},
                    function ()
                    {
                        swal.close();
                        leaveTypeTable(2, profileno);
                        fetchLeaveTypes();
                    });
        }
    });
}


function updateEmployeeCredit(profileno, index, option) {
    if (option == 1) {
        $('input[name=' + index + '_credit]').attr('readonly', false);
        $('span[name=' + index + '_update]').removeClass('hidden');
        $('span[name=' + index + '_cancel]').removeClass('hidden');
        $('span[name=' + index + '_edit]').addClass('hidden');
    } else if (option == 4) {
        $('input[name=' + index + '_credit]').attr('readonly', true);
        $('span[name=' + index + '_update]').addClass('hidden');
        $('span[name=' + index + '_cancel]').addClass('hidden');
        $('span[name=' + index + '_edit]').removeClass('hidden');
    } else if (option == 3) {
        updateEmployeeCreditCount(profileno, index);
    }

}


$('span[name=save_new_leavetype]').click(function () {
    if ($('input[name=new_leavetype]').val() == '') {
        $('label[name=add_leavetpye_error]').empty();
        $('label[name=add_leavetpye_error]').append('Leave type name required.');
    } else {
        $('label[name=add_leavetpye_error]').empty();
        leave_name = $('input[name=new_leavetype]').val();
        id = 0;
        saveLeaveType();
    }
}
);

