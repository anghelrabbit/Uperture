
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

function activateAccount(element, id) {
//    $.ajax({
//        data: {id: id, action: $(element).val()},
//        url: 'AccountApproval/ApproveAccount',
//        type: 'POST',
//        dataType: 'json'
//    }).done(function () {
//        var action_text = 'returned to Pending';
//        if ($(element).val() == 1) {
//            action_text = 'Approved';
//        } else if ($(element).val() == 2) {
//            action_text = 'Declined';
//
//        }
//        swal({title: "Success",
//            text: "Account " + action_text + ".",
//            type: "success",
//            show: true,
//            backdrop: 'static',
//            timer: 1600,
//            showConfirmButton: false,
//            keyboard: false},
//                function ()
//                {
//                    swal.close();
//                    fetchRegistry();
//                });
//
//    });

console.log("here");
}



//Angel Bunny activate or deny an account



function actionForAccount() {
//    $.ajax({
//        data: {id: id, action: action},
//        url: 'AccountApproval/ApproveAccount',
//        type: 'POST',
//        dataType: 'json'
//    }).done(function () {
//        
//        swal({title: "Success",
//            type: "success",
//            show: true,
//            backdrop: 'static',
//            timer: 1600,
//            showConfirmButton: false,
//            keyboard: false},
//                function ()
//                {
//                    swal.close();
//                    fetchRegistry();
//                });
//
//    });

console.log("here");
}