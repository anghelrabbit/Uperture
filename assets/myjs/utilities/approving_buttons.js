/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var approved_status = '';

function userApproval(result, head, hr, supervisor, reliever, form) {
    approved_status = result['approved_status'];
    if (form == 2) {
        setupDiv(1);
        manipulateButton('reliever_buttons', result['reliever_status'], '', result['reliever_date_status'], reliever, form, 'reliever');
        manipulateButton('reliever_cancel', result['reliever_cancel_status'], '', result['reliever_cancel_date'], reliever, form, 'reliever');
    } else {
        setupDiv(0);
    }
    manipulateButton('supervisor_buttons', result['counter_signed_status'], result['counter_signed_by'], result['counter_sign_date'], supervisor, form, 'Supervisor');
    manipulateButton('head_buttons', result['noted_status'], result['noted_by'], result['noted_date'], head, form, 'Head');
    manipulateButton('hr_buttons', result['approved_status'], result['approved_by'], result['approved_date'], hr, form, 'HR');

    manipulateButton('supervisor_cancel', result['supervisor_cancel_status'], result['supervisor_name_deleter'], result['supervisor_delete_date'], supervisor, form, 'Supervisor');
    manipulateButton('head_cancel', result['head_cancel_status'], result['head_name_deleter'], result['head_delete_date'], head, form, 'Head');
    manipulateButton('hr_cancel', result['hr_cancel_status'], result['hr_name_deleter'], result['hr_delete_date'], hr, form, 'HR');

}

function setupDiv(index) {
    if (index == 0) {
        $('div[name=reliever_signatory_div]').addClass('hidden');
        $('div[name=reliever_cancel_div]').addClass('hidden');
        $('.signatory_div').removeClass('col-md-3');
        $('.cancel_div').removeClass('col-md-3');
        $('.signatory_div').addClass('col-md-4');
        $('.cancel_div').addClass('col-md-4');
    } else {
        $('div[name=reliever_signatory_div]').removeClass('hidden');
        $('div[name=reliever_cancel_div]').removeClass('hidden');
        $('.signatory_div').removeClass('col-md-4');
        $('.cancel_div').removeClass('col-md-4');
        $('.signatory_div').addClass('col-md-3');
        $('.cancel_div').addClass('col-md-3');

    }
}


function manipulateButton(holder, status, by, date, userlevel, index, category) {
    console.log(holder);
    console.log(status);
    console.log(userlevel);
    $('div[name=' + holder + ']').empty();

    if (status == 0 || status == null) {
        if (userlevel == 1) {
            $('div[name=' + holder + ']').append('<span class="btn" style="background-color:#3ED03E;color:white" onclick="approveDeclineForm(' + "'" + category + "','" + index + "','1'" + ')">Approve</span>&nbsp;\n\
    <span class="btn" style="background-color:#F8665E;color:white" onclick="approveDeclineForm(' + "'" + category + "','" + index + "','2'" + ')">Decline</span>');
        } else {
            $('div[name=' + holder + ']').append('Approval Pending');
        }
    } else if (status == 1) {
        if (category == 'reliever') {
            $('div[name=' + holder + ']').append('Approved on ' + date);
        } else {
            $('div[name=' + holder + ']').append(by + '<br> Approved on ' + date);

        }
    } else if (status == 2) {
        $('div[name=' + holder + ']').append(by + '<br> Declined on ' + date);
    }


}

function selectedFormButtons(result, category) {
    var form = '';
    var form_title = '';
    if (category == 0) {
        form = 'leave';
        form_title = 'Leave';
    } else if (category == 1) {
        form = 'undertime';
        form_title = 'Undertime';
    } else if (category == 2) {
        form = 'cs';
        form_title = 'Change Schedule';
    } else if (category == 3) {
        form = 'overtime';
        form_title = 'Overtime';
    }
    $('button[name=save_' + form + ']').addClass('hidden');
    $('button[name=update_' + form + ']').removeClass('hidden');
    $('button[name=remove_' + form + ']').removeClass('hidden');
    $('button[name=cancel_' + form + ']').addClass('hidden');

    if (result['is_updated'] == 1) {
        if (category != 2) {
            $('button[name=cancel_' + form + ']').removeClass('hidden');
        }
        $('button[name=update_' + form + ']').addClass('hidden');
        $('button[name=remove_' + form + ']').addClass('hidden');
        if (result['approved_status'] == 2) {
            $('button[name=cancel_' + form + ']').addClass('hidden');
            $('button[name=update_' + form + ']').addClass('hidden');
            $('button[name=remove_' + form + ']').removeClass('hidden');
        }
    }

    if (result['is_deleted'] == 1) {
        $('h4[name=' + form + '_title]').empty();
        if (result['hr_cancel_status'] == 1) {
            $('h4[name=' + form + '_title]').append(form_title + ' Form <span style="color:red">(Cancelled)</span>');
        } else {
            $('h4[name=' + form + '_title]').append(form_title + ' Form <span style="color:yellow">(For Cancellation)</span>');
        }
        $('div[name=cancellation_content]').removeClass('hidden');
        $('button[name=cancel_' + form + ']').addClass('hidden');
    } else {
        $('div[name=cancellation_content]').addClass('hidden');
    }
}


function approveDeclineForm(user, index, action) {
    swal({
        title: "Are you sure?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        confirmButtonText: "Yes Proceed.",
        closeOnConfirm: false
    }, function () {
        $.ajax({

            type: 'POST',
            data: {
                id: form_id,
                user: user,
                action: action,
                table: index,
                for_cancellation: tab_category,
                payment_type: $('select[name=leave_payment_type]').val()

            },
            url: 'PendingForms/ApproveDeclineForm',
            dataType: 'json'

        }).done(function (result) {
            var text = 'Approved';
            if (action == 2) {
                text = 'Declined';
            }
            if (result) {
                swal({title: "Success",
                    text: "Request " + text + ".",
                    type: "success",
                    show: true,
                    backdrop: 'static',
                    timer: 1600,
                    showConfirmButton: false,
                    keyboard: false},
                        function ()
                        {
                            swal.close();
                            if (index == 0) {
                                leave_table.ajax.reload(null, false);
                                $('div[name=leave_modal]').modal('hide');
                            } else if (index == 1) {
                                undertime_table.ajax.reload(null, false);
                                $('div[name=undertime_modal]').modal('hide');
                            } else if (index == 2) {
                                change_schedule_table.ajax.reload(null, false);
                                $('div[name=modal_cs]').modal('hide');
                            } else if (index == 3) {
                                overtime_table.ajax.reload(null, false);
                                $('div[name=modal_overtime]').modal('hide');
                            }
                            countNotification();
                        });
            }
        });
    }
    );

}

