
$(function () {
    checkIfInstallment();
    fetchPendingReimbursementRequest();
});


$('button[name=add_reimbursement]').click(function () {
    $('div[name=reimbursement_modal]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });

});


function saveReimbursementRequest() {
//console.log($('#reimbursement_for').val());

    $.ajax({
        data: {reimbursement_for: $('#reimbursement_for').val(),
            reimbursement_amount: $('#reimbursement_amount').val(),
            reimbursement_payment_mode: $('#reimbursement_payment_mode').val(),
            reimbursement_amount_to_pay: $('#reimbursement_amount_to_pay').val(),
            reimbursement_regularity: $('#reimbursement_regularity').val(),

        },

        url: 'RequestReimbursement/SaveReimbursementRequest',
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
                    fetchPendingReimbursementRequest();
                    $('div[name=reimbursement_modal]').modal('hide');

                });

    });
}

function checkIfInstallment() {

    if ($('#reimbursement_payment_mode').val() == 2) {

        $('#reimbursement_amount_to_pay').removeAttr('disabled');
        $('#reimbursement_regularity').removeAttr('disabled');
    } else if ($('#reimbursement_payment_mode').val() == 1) {
        $('#reimbursement_amount_to_pay').attr('disabled', true);
        $('#reimbursement_regularity').attr('disabled', true);

        

    }
}

function fetchPendingReimbursementRequest() {
    
//    console.log($('#select_payment_mode').val());
    
    $('#pending_reimbursement_request_table').DataTable().clear().destroy();
    $('#pending_reimbursement_request_table').DataTable({
        dom: 'frtip',
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],
        ajax: {
            url: 'RequestReimbursement/FetchPendingReimbursementRequest',
            type: 'POST',
            data: {select_payment_mode : $('#select_payment_mode').val()},
        },

    });
    $('#pending_reimbursement_request_table_filter').empty();
}

function viewReimbursementdetails(reimbursement_id){
    $('div[name=modal_view_reimbursement_detail]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
}