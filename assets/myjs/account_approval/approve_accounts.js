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

function activateAccount(action, id) {

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
        approveTheAccount(action, id);
    }

}


function denyTheAccount(action, id) {
    $.ajax({
        data: {id: id, action: action},
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
                    fetchRegistry();
                });

    });
}

function approveTheAccount(action, id) {

    $('div[name=modal_add_account_details]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
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