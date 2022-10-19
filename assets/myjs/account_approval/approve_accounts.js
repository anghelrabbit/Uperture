/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(function () {
    fetchRegistry();
});
function fetchRegistry() {
    $('#account_approve_table').DataTable().clear().destroy();
    $('#account_approve_table').DataTable({
        dom: 'frtip',
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],
        ajax: {
            url: 'AccountApproval/FetchApproval',
            type: 'POST',
        },

    });
    $('#account_approve_table_filter').empty();
}

function activateAccount(action, id, fullname, email) {

    if (action == 2) {
        swal({
            title: "Delete this applicant?",
            text: "Are you sure you want to delete this record?",
            showCancelButton: true,
            confirmButtonColor: "#5cb85c",
            confirmButtonText: "YES",
            cancelButtonText: "No",
            closeOnConfirm: false,
            closeOnCancel: true,
        }, function (isConfirm) {
            if (isConfirm) {
                denyTheAccount(action, id);
            }
        });
    } else {
        approveTheAccount(action, id, fullname, email);
    }

}


function denyTheAccount(action, id) {
    $.ajax({
        data: {id: id, action: action},
        url: 'AccountApproval/DenyAccount',
        type: 'POST',
        dataType: 'json'
    }).done(function () {

        swal({title: "Success",
            type: "success",
            show: true,
            backdrop: 'static',
            timer: 1600,
            showConfirmButton: false,
            keyboard: false},
                function ()
                {
                    swal.close();
                    fetchRegistry();
                });

    });
}

function approveTheAccount(action, id, fullname, email) {

    $('div[name=modal_add_account_details]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    $('#acc_referall_person').empty();
    $('#txtprofileno').val(id);
    $('#txtaction').val(action);
    $('#acc_emp_name').val(fullname);
    $('#acc_email_add').val(email);
    fetchAllEmployees();

}

function fetchAllEmployees() {

    $('#acc_referall_person').append(
            "<option  value='0'>None</option>"
            );

    $.ajax({
        type: 'POST',
        url: 'AccountApproval/FetchAllEmployees',
        data: {},
        dataType: 'json',
    }).done(function (result) {


        for (var index = 0; index <= result.length - 1; index++) {
//            console.log();
            $('#acc_referall_person').append(
                    "<option value=" + result[index].profileno + ">" + result[index].firstname + " " + result[index].lastname + "</option>"
                    );
        }


    });

}
function fetchProfileNoDetails() {
    $.ajax({
        type: 'POST',
        url: 'AccountApproval/FetchProfileNoDetails',
        data: {profileno: $('#txtprofileno').val()},
        dataType: 'json',
    }).done(function (result) {

    });

}

function approveEmployeeAcc() {


    var image = $('#acc_resume').prop('files')[0];
    var form = document.getElementById("add_account_form");
    var form_data = new FormData(form);
    form_data.append("file", image, $('#acc_resume').val());


    $.ajax({
        data: {action: $('#txtaction').val(),
            acc_doh: $('#acc_doh').val(),
            acc_department: $('#acc_department').val(),
            acc_position_stat: $('#acc_position_stat').val(),
            acc_job_status: $('#acc_job_status').val(),
            acc_pay_period: $('#acc_pay_period').val(),
            acc_referall_person: $('#acc_referall_person').val(),
            txtprofileno: $('#txtprofileno').val(),
            acc_resume: $('#acc_resume').val()
        },
       
        
        url: 'AccountApproval/ApproveAccount',
        type: 'POST',
        dataType: 'json'
    }).done(function () {

        swal({title: "Success",
            type: "success",
            show: true,
            backdrop: 'static',
            timer: 1600,
            showConfirmButton: false,
            keyboard: false},
                function ()
                {
                    swal.close();
                    $('div[name=modal_add_account_details]').modal('hide');
                    fetchRegistry();
                });

    });
}


function closeModal() {
    swal({
        title: "Exit this form?",
        text: "Are you sure you want to disregard all information?",
        showCancelButton: true,
        confirmButtonColor: "#5cb85c",
        confirmButtonText: "YES",
        cancelButtonText: "No",
        closeOnConfirm: false,
        closeOnCancel: true,
    }, function (isConfirm) {
        if (isConfirm) {
            swal.close();
            $('div[name=modal_add_account_details]').modal('hide');
        } else {
            swal.close();
        }
    });

}