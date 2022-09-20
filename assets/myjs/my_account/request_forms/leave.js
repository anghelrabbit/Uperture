
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var form_category = 0;
var for_cancellation = 0;
var form = 0;
var has_schedule = false;
var signatory_template = $('#signatory-template').html();
var previous_payment_type = 0;
var credits = {};
var date_excluded = {};
var upon_request_credit = {};
var request_form_id = 0;
var previous_paytype = 0;

$(function () {

    fetchMyLeaveCredits();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'LeaveCredits/FetchTypeOfLeave'
    }).done(function (result) {
        $('select[name=leave_type]').empty();
        $.each(result, function (key, value) {
            $('select[name=leave_type]').append('<option value="' + value['name'] + "/" + value['id'] + '">' + value['name'] + '</option>');
        });
    });
    setupPayPeriod().done(function () {
        tabCategory('');
        by_cutoff = 1;
    });
});
function fetchMyLeaveCredits() {
    $.ajax({
        data: {profileno: 0},
        type: 'POST',
        dataType: 'json',
        url: 'LeaveCredits/FetchSpecificLeaveCredits'
    }).done(function (result) {
        $('div[name=availablle_leave_credits]').empty();
        $.each(result, function (key, value) {
            $('div[name=availablle_leave_credits]').append('  <div class="col-lg-3 col-md-4 col-xs-12">' +
                    '<div class="small-box" style="background-color:#2692D0; color:white">' +
                    '<div class="inner">' +
                    '<div class="row"><div class="col-lg-12 col-md-12 col-xs-2"><span  style="font-size:10px;letter-spacing:1px;font-weight:bold">Available</span></div>' +
                    '<div class="col-lg-9 col-md-9 col-xs-8"><span  style="font-size:15px;letter-spacing:0.5px">' + key + '</span></div>' +
                    '<div class="col-lg-3 col-md-3 col-xs-2"><span class="" style="font-size:15px;font-weight:bold">' + value['remaining_days'] + '/' + value['total'] + '</span></div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>');
            credits[key] = value['remaining_days'];

        });


    });
}



function fetchMyLeave() {
    fetchMyLeaveCredits();
    $('#my_leave_table').DataTable().clear().destroy();
    $('#my_leave_table').DataTable({
        dom: 'frtip',
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],
        "columnDefs": [
            {"visible": false, "targets": 0}
        ],
        ajax: {
            url: 'Leave/FetchMyLeave',
            type: 'POST',
            data: {
                datein: $('#worksched_in').val(),
                dateout: $('#worksched_out').val()
            },

        },
        createdRow: function (row, data, dataIndex)
        {
            if (data[0] > 0) {
                $(row).css('background-color', '#ff8785');
            }
        },
        onComplete: function (settings, json) {
            $('div[name=loading_overlay]').addClass('hidden');
        }

    });
    $('#my_leave_table_filter').empty();
}

function removeLeave() {
    swal({
        title: "Are you sure?",
        text: "Form will not be retrieved.",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes Proceed.",
        closeOnConfirm: false
    }, function () {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'Leave/RemoveSpecificLeave',
            data: {id: request_form_id}
        }).done(function () {
            swal({title: "Success",
                text: "Form removed.",
                type: "success",
                show: true,
                backdrop: 'static',
                timer: 1600,
                showConfirmButton: false,
                keyboard: false},
                    function ()
                    {
                        $('div[name=leave_modal]').modal('hide');
                        swal.close();
                        tabCategory(0);
                    });

        });
    });

}


function setLeaveDates(dates) {
    $('div[name=leave_dates]').empty();
    $.each(dates, function (key, value) {
        var val = '';
        if (value == '') {
            val = '<input type="text" class="form-control" value ="Day off"  readonly>';
        } else if (value == 'Approved Leave' || value == 'Pending Leave') {
            val = '<input type="text" class="form-control" style="color:red" value ="' + value + '"  readonly>';
        } else {
            val = '<input type="text" class="form-control" style=""  value ="' + value + '"  readonly>';
        }

        $('div[name=leave_dates]').append(
                '<div class="input-group ">' +
                '<div class="  btn btn-info input-group-addon">' +
                '<span class="" id="basic-addon2">' + key + '</span>' +
                '</div>' +
                val +
                '</div>'

                );
    });
}

function checkLeaveDates() {
    dateRestrictions();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'Leave/CheckLeaveDates',
        data: {
            datein: $('input[name=leave_datefrom]').val(),
            dateout: $('input[name=leave_dateto]').val(),
            id: request_form_id
        }

    }).done(function (result) {
        date_excluded = result['excluded'];
        $('input[name=total_days]').val(result['total_days']);
        setLeaveDates(result['dates']);
    });
}
function saveUpdateLeave() {
    if (date_excluded > 0) {
        swal({title: "Error",
            text: "Existing Leave on Specified Dates.",
            type: "error",
            show: true,
            backdrop: 'static',
            timer: 2000,
            showConfirmButton: false,
            keyboard: false},
                function ()
                {
                    swal.close();
                });
    } else {
        $('div[name=loading_modal]').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'Leave/SaveUpdateLeave',
            data: {
                id: request_form_id,
                leave_type: $('select[name=leave_type]').val(),
                leave_ifothers: $('input[name=leave_ifothers]').val(),
                leave_credit: credits[$('select[name=leave_type]').val()],
                leave_day_category: $('select[name=leave_day_category]').val(),
                leave_payment_type: $('select[name=leave_payment_type]').val(),
                leave_datefrom: $('input[name=leave_datefrom]').val(),
                leave_dateto: $('input[name=leave_dateto]').val(),
                total_days: $('input[name=total_days]').val(),
                previous_total: $('input[name=previous_total]').val(),
                leave_reason: $('textarea[name=leave_reason]').val(),
                previous_payment_type: previous_payment_type,
                previous_paytype: previous_paytype,
            }
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
                if ('insufficient_credit' in data) {
                    swal({title: "Error",
                        text: "Insufficient " + data['messages'] + " credits.",
                        type: "error",
                        show: true,
                        backdrop: 'static',
                        timer: 2000,
                        showConfirmButton: false,
                        keyboard: false},
                            function ()
                            {
                                swal.close();
                            });
                }
            } else {
                swal({title: "Success",
                    text: "Leave saved.",
                    type: "success",
                    show: true,
                    backdrop: 'static',
                    timer: 1600,
                    showConfirmButton: false,
                    keyboard: false},
                        function ()
                        {
                            $('div[name=leave_modal]').modal('hide');
                            swal.close();
                            tabCategory(0);
                        });
            }
        });
    }
}

function fetchSpecificLeave(id) {
    $('#leave_signatory').empty();
    $('#leave_signatory').append(signatory_template);
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'Leave/FetchSpecificLeave',
        data: {
            id: id,
        }
    }).done(function (result) {
        request_form_id = id;
        setLeaveDates(result['dates']);
        refreshLeaveForm();
        userApproval(result['data'], 0, 0, 0, 1);
        $('div[name=leave_modal]').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
        $('select[name=leave_type]').val(result['data']['leavetype']);
        $('input[name=leave_datefrom]').val(result['data']['fromdate']);
        $('input[name=leave_dateto]').val(result['data']['todate']);
        $('input[name=previous_total]').val(result['data']['leavedays']);
        $('input[name=total_days]').val(result['data']['leavedays']);
        $('select[name=leave_day_category]').val(result['data']['day_type']);
		$('textarea[name=leave_reason]').val(result['data']['reason']);
        if ('ifothers' in result['data']) {
            $('input[name=leave_ifothers]').attr('readonly', false);
            $('input[name=leave_ifothers]').val(result['data']['ifothers']);
        } else {
            $('input[name=leave_ifothers]').attr('readonly', true);
            $('input[name=leave_ifothers]').val('');
        }
        previous_paytype = result['data']['payment_type'];
        previous_payment_type = result['data']['payment_type'];
        dateRestrictions();
        $('select[name=leave_payment_type]').val(result['data']['payment_type']);
		


        selectedFormButtons(result['data'], 0);
        $('select[name=leave_type]').change();

    });
}


function refreshLeaveForm() {

    $('div[name=supervisor_buttons]').empty();
    $('div[name=head_buttons]').empty();
    $('div[name=hr_buttons]').empty();

    $('div[name=supervisor_buttons]').append('Pending Approval');
    $('div[name=head_buttons]').append('Pending Approval');
    $('div[name=hr_buttons]').append('Pending Approval');

    $('div[name=cancellation_content]').addClass('hidden');

    $('button[name=save_leave]').removeClass('hidden');
    $('button[name=update_leave]').addClass('hidden');
    $('button[name=remove_leave]').addClass('hidden');
    $('button[name=cancel_leave]').addClass('hidden');
    $('h4[name=leave_title]').empty();
    $('h4[name=leave_title]').append('Leave Form');

    $('label[name=leave_datefrom_error]').empty();
    $('label[name=leave_dateto_error]').empty();
    $('select[name=leave_day_category]').val(1);
    $('select[name=leave_payment_type]').val(0);
    $('textarea[name=leave_reason]').val('');
    if ($('select[name=leave_type]').val() == 'Others') {
        $('input[name=leave_ifothers]').attr('readonly', false);
    } else {
        $('input[name=leave_ifothers]').attr('readonly', true);
        $('input[name=leave_ifothers]').val('');
    }

}

function dateRestrictions() {
    if ($('select[name=leave_day_category]').val() == 1) {
        $('input[name=leave_dateto]').attr('readonly', true);
        $('input[name=leave_dateto]').val($('input[name=leave_datefrom]').val());
    } else {
        $('input[name=leave_dateto]').removeAttr('readonly', false);
        if ($('input[name=leave_dateto]').val() == '' || $('input[name=leave_datefrom]').val() > $('input[name=leave_dateto]').val()) {
            $('input[name=leave_dateto]').val($('input[name=leave_datefrom]').val());
        }
    }
}




function removeForm() {
    swal({
        title: "Are you sure?",
        text: "Form will not be retrieved.",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes Proceed.",
        closeOnConfirm: false
    },
            function () {
                $.ajax({
                    type: 'POST',
                    data: {
                        id: request_form_id,
                        category: form_category
                    },
                    url: 'RequestForms/RemoveForm',
                    dataType: 'json'

                }).done(function (result) {

                    swal({title: "Success",
                        text: "Form removed.",
                        type: "success",
                        show: true,
                        backdrop: 'static',
                        timer: 1600,
                        showConfirmButton: false,
                        keyboard: false},
                            function ()
                            {
                                if (form_category == 1) {
                                    $('div[name=undertime_modal]').modal('hide');
                                } else if (form_category == 2) {
                                    $('div[name=modal_cs]').modal('hide');
                                } else if (form_category == 3) {
                                    $('div[name=modal_overtime]').modal('hide');
                                }
                                swal.close();
                                tabCategory(form_category);
                            });
                });
            });
}

function cancelForm() {
    swal({
        title: "Are you sure to cancel form?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes Proceed.",
        closeOnConfirm: false
    },
            function () {
                $('#Searching_Modal').modal({
                    show: true,
                    backdrop: 'static',
                    keyboard: false
                });
                $.ajax({

                    type: 'POST',
                    data: {
                        id: request_form_id,
                        category: form_category

                    },
                    url: 'RequestForms/RequestCancellation',
                    dataType: 'json'

                }).done(function (result) {
                    $('#Searching_Modal').modal('hide');
                    if (result) {
                        swal({title: "Success",
                            text: "Request granted.",
                            type: "success",
                            show: true,
                            backdrop: 'static',
                            timer: 1600,
                            showConfirmButton: false,
                            keyboard: false},
                                function ()
                                {
                                    if (form_category == 0) {
                                        $('div[name=leave_modal]').modal('hide');
                                    } else if (form_category == 1) {
                                        $('div[name=undertime_modal]').modal('hide');
                                    } else if (form_category == 2) {
                                        $('div[name=modal_cs]').modal('hide');
                                    } else if (form_category == 3) {
                                        $('div[name=modal_overtime]').modal('hide');
                                    }
                                    swal.close();
                                    tabCategory(form_category);
                                });

                    }
                });
            });
}

function tabCategory(category) {
    if (category != '') {
        form_category = category;
    }
    if (form_category == 5) {
        fetchEmployees();
    } else if (form_category == 0) {
        $('li[name=leave_li]').click();
    } else if (form_category == 1) {
        $('li[name=undertime_li]').click();
    } else if (form_category == 2) {
        $('li[name=cs_li]').click();
    } else if (form_category == 3) {
        $('li[name=ot_li]').click();
    }

}
$('li[name=leave_li], li[name=undertime_li], li[name=cs_li], li[name=ot_li]').click(function () {
    $('div[name=loading_overlay]').removeClass('hidden');
    var category = {
        'Leave': {'my_function': fetchMyLeave(), 'category': 0},
        'Undertime': {'my_function': fetchMyUndertime(), 'category': 1},
        'Change Schedule': {'my_function': fetchMyChangeSchedule(), 'category': 2},
        'Overtime': {'my_function': fetchMyOvertime(), 'category': 3}
    };
    category[$(this).text()].my_function;
    form_category = category[$(this).text()].category;
    helpdesk_carousel(category[$(this).text()].category);

});


$('button[name=save_leave]').click(function () {
    saveUpdateLeave();
});
$('button[name=update_leave]').click(function () {
    saveUpdateLeave();
});

$('button[name=remove_leave]').click(function () {
    removeLeave();
});
$('button[name=add_leave]').click(function () {
    $('div[name=credits_title]').empty();
    $('div[name=credits_title]').append('Remaining Credits');
    $('div[name=leave_modal]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    form = 0;
    request_form_id = 0;
    for_cancellation = 0;
    $('h4[name=leave_title]').empty();
    $('h4[name=leave_title]').append('Leave Form');
    $('#leave_signatory').empty();
    $('#leave_signatory').append(signatory_template);
    refreshLeaveForm();
    checkLeaveDates();
    setupDiv(0);
    $('select[name=leave_type]').change();
});




$('input[name=leave_datefrom]').change(function () {
    checkLeaveDates();
});
$('input[name=leave_dateto]').change(function () {
    checkLeaveDates();
});

$('select[name=leave_day_category]').change(function () {
    dateRestrictions();
    checkLeaveDates();
});

$('button[name=cancel_leave]').click(function () {
    cancelForm();
});


$('select[name=leave_type]').change(function () {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'LeaveCredits/FetchLeaveCreditCount',
        data: {
            leavetype: $('select[name=leave_type]').val(),
        }
    }).done(function (result) {
        $('select[name=leave_payment_type]').empty();
        $('select[name=leave_payment_type]').append('<option value="0">Without Pay</option>');
        if (result['credit_name'] == 'Others') {
            $('input[name=leave_ifothers]').attr('readonly', false);
        } else {
            $('input[name=leave_ifothers]').attr('readonly', true);

        }
        if (result['count'] > 0) {
            $('select[name=leave_payment_type]').append(
                    '<option value="1">With Pay</option>');

        }
    });

});

