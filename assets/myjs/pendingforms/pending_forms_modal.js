
function leaveModal(head, hr, supervisor, reliever, id) {
    $('div[name=leave_approval_content]').empty();
    $('div[name=leave_approval_content]').append(signatory_template);
    signatoryRestriction();
    $.ajax({

        type: 'POST',
        url: "Leave/FetchSpecificLeave",
        data: {id: id,

        },
        dataType: 'json'

    }).done(function (res) {
        var result = res['data'];

        $('.modal-footer').empty();
        $('div[name=leave_modal]').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
        $('select[name=leave_payment_type]').empty();
        if (parseInt(result['payment_type']) == 0) {
            $('select[name=leave_payment_type]').append('<option value ="0">Without Pay</option>');
        } else {
            $('select[name=leave_payment_type]').append('<option value="1">With Pay</option>');
        }
        if ('ifothers' in result) {
            $('input[name=leave_ifothers]').val(result['ifothers']);
        } else {
            $('input[name=leave_ifothers]').val('');
        }
        $('select[name=leave_type]').empty();
        var leavetype = result['leavetype'].split("/");
        $('select[name=leave_type]').append('<option>' + leavetype[0] + '</option>');
        $('input[name=leave_empname]').val(result['empname']);
        $('input[name=leave_compname]').val(result['company']);
        $('input[name=leave_jobpos]').val(result['jobpos']);
        $('input[name=total_days]').val(result['leavedays']);
        $('div[name=div_day_category]').addClass('hidden');
        $('input[name=leave_datefrom]').val(result['fromdate']);
        $('input[name=leave_datefrom]').attr('readonly', true);
        $('input[name=leave_dateto]').val(result['todate']);
        $('input[name=leave_dateto]').attr('readonly', true);
        $('textarea[name=leave_reason]').removeAttr('onkeyup');
        $('textarea[name=leave_reason]').prop('disabled', true);
        $('textarea[name=leave_reason]').val(result['reason']);
        setLeaveDates(res['dates']);
        form_id = id;
        for_cancellation = result['is_deleted'];
        userApproval(result, head, hr, supervisor, reliever, 0);

    });
}



function undertimeModal(supervisor, head, hr, reliever, id) {
    $('div[name=undertime_approval_content]').empty();
    $('div[name=undertime_approval_content]').append(signatory_template);
    signatoryRestriction();
    $.ajax({

        type: 'POST',
        data: {
            id: id
        },
        url: 'UnderTime/FetchSpecificUndertime',
        dataType: 'json'

    }).done(function (result) {
        $('.modal-footer').empty();


        $('div[name=undertime_modal]').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
        $('a[href="#undertime_details"]').click();
        $('input[name=undertime_employeename]').val(result['empname']);
        $('input[name=undertime_company]').val(result['company']);

        $('input[name=undertime_worksched_datein]').val(result['sched_datein']);
        $('input[name=undertime_worksched_datein]').removeAttr('onchange');
        $('input[name=undertime_worksched_datein]').attr('disabled', true);

        $('input[name=undertime_worksched_dateout]').val(result['sched_dateout']);
        $('input[name=undertime_worksched_timein]').val(result['sched_timein']);
        $('input[name=undertime_worksched_out]').val(result['sched_timeout']);


        $('input[name=undertime_actual_datein]').addClass('hidden');
        $('input[name=undertime_actual_dateout]').addClass('hidden');
        $('input[name=undertime_actualin]').addClass('hidden');
        $('input[name=undertime_actualout]').addClass('hidden');

        $('input[name=undertime_datein_disable]').removeClass('hidden');
        $('input[name=undertime_dateout_disable]').removeClass('hidden');
        $('input[name=undertime_actualin_disable]').removeClass('hidden');
        $('input[name=undertime_actualout_disable]').removeClass('hidden');

        $('input[name=undertime_datein_disable]').val(result['actual_datein']);
        $('input[name=undertime_dateout_disable]').val(result['actual_dateout']);
        $('input[name=undertime_actualin_disable]').val(result['actual_timein']);
        $('input[name=undertime_actualout_disable]').val(result['actual_timeout']);


        $('select[name=undertime_type]').val(result['undertime_type']);
        $('select[name=undertime_type]').prop("disabled", true);

        $('textarea[name=undertime_reason]').val(result['reason']);
        $('textarea[name=undertime_reason]').attr('readonly', true);



        form_id = id;
        for_cancellation = result['is_deleted'];

        userApproval(result, head, hr, supervisor, reliever, 1);


    });

}



function changescheduleModal(supervisor, head, hr, reliever, id) {
    $('div[name=cs_approval_content]').empty();
    $('div[name=cs_approval_content]').append(signatory_template);
    signatoryRestriction();
    $.ajax({

        type: 'POST',
        data: {
            id: id,

        },
        url: 'ChangeSchedule/FetchSpecificCS',
        dataType: 'json'

    }).done(function (result) {
        $('.modal-footer').empty();
        var data = result['info'];
        $('div[name=modal_cs]').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
        form_id = id;
        for_cancellation = result['is_deleted'];
        $('input[name=cs_date_filed]').val(data['date_requested']);
        $('input[name=cs_employeename]').val(data['empname']);
        $('input[name=cs_company]').val(data['company']);
        $('span[name=reliever_btn]').parent().empty();
        $('input[name=cs_relievername]').parent().removeClass('input-group');
        $("input[name=shiftchange]").prop("checked", false);
        $("input[name=straightduty]").prop("checked", false);
        $("input[name=canceldayoff]").prop("checked", false);
        $("input[name=changedayoff]").prop("checked", false);
        $("input[name=shiftchange]").attr("disabled", true);
        $("input[name=straightduty]").attr("disabled", true);
        $("input[name=canceldayoff]").attr("disabled", true);
        $("input[name=changedayoff]").attr("disabled", true);

        if (data['shiftchange'] == 1) {
            $("input[name=shiftchange]").prop("checked", true);
            shiftchange = 1;
        }
        if (data['straightduty'] == 1) {
            $("input[name=straightduty]").prop("checked", true);
            straightduty = 1;
        }
        if (data['canceldayoff'] == 1) {
            $("input[name=canceldayoff]").prop("checked", true);
            canceldayoff = 1;
        }
        if (data['changedayoff'] == 1) {
            $("input[name=changedayoff]").prop("checked", true);
            changedayoff = 1;
        }

        $('input[name=cs_fromshift_datein]').attr('readonly', true);
        $('input[name=cs_toshift_timein]').attr('readonly', true);
        $('input[name=cs_toshift_timeout]').attr('readonly', true);
        $('input[name=cs_toshift_datein]').attr('readonly', true);
        $('input[name=cs_toshift_dateout]').attr('readonly', true);
        $('textarea[name=cs_reason]').attr('readonly', true);
        $('input[name=cs_fromshift_datein]').val(data['worksched_datein']);
        $('input[name=cs_fromshift_dateout]').val(data['worksched_dateout']);
        $('input[name=cs_fromshift_timein]').val(result['worksched']['time_in']);
        $('input[name=cs_fromshift_timeout]').val(result['worksched']['time_out']);
        $('input[name=cs_fromshift_timein_dayoff]').val(result['worksched']['time_in']);
        $('input[name=cs_fromshift_timeout_dayoff]').val(result['worksched']['time_out']);
        $('input[name=cs_relievername]').val(data['reliever_name']);

        $('input[name=cs_toshift_datein]').val(data['toshift_datein']);
        $('input[name=cs_toshift_dateout]').val(data['toshift_dateout']);
        $('input[name=cs_toshift_timein]').val(result['toshift']['time_in']);
        $('input[name=cs_toshift_timeout]').val(result['toshift']['time_out']);
        $('input[name=cs_toshift_timein_dayoff]').val(result['toshift']['time_in']);
        $('input[name=cs_toshift_timeout_dayoff]').val(result['toshift']['time_out']);
        $('input[name=cs_relievername]').val(data['reliever_name']);
        $('textarea[name=cs_reason]').val(data['reason']);

        if (result['worksched']['time_in'] == 'Day Off') {
            $('input[name=cs_fromshift_timein]').addClass('hidden');
            $('input[name=cs_fromshift_timein_dayoff]').removeClass('hidden');
        } else {
            $('input[name=cs_fromshift_timein]').removeClass('hidden');
            $('input[name=cs_fromshift_timein_dayoff]').addClass('hidden');

        }
        if (result['worksched']['time_out'] == 'Day Off') {
            $('input[name=cs_fromshift_timeout]').addClass('hidden');
            $('input[name=cs_fromshift_timeout_dayoff]').removeClass('hidden');
        } else {
            $('input[name=cs_fromshift_timeout]').removeClass('hidden');
            $('input[name=cs_fromshift_timeout_dayoff]').addClass('hidden');

        }

        if (result['toshift']['time_in'] == 'Day Off') {
            $('input[name=cs_toshift_timein]').addClass('hidden');
            $('input[name=cs_toshift_timein_dayoff]').removeClass('hidden');
        } else {
            $('input[name=cs_toshift_timein]').removeClass('hidden');
            $('input[name=cs_toshift_timein_dayoff]').addClass('hidden');

        }
        if (result['toshift']['time_out'] == 'Day Off') {
            $('input[name=cs_toshift_timeout]').addClass('hidden');
            $('input[name=cs_toshift_timeout_dayoff]').removeClass('hidden');
        } else {
            $('input[name=cs_toshift_timeout]').removeClass('hidden');
            $('input[name=cs_toshift_timeout_dayoff]').addClass('hidden');

        }

        userApproval(result['info'], head, hr, supervisor, reliever, 2);



    });
}

function overtimeModal(supervisor, head, hr, idx) {
    $('div[name=overtime_approval_content]').empty();
    $('div[name=overtime_approval_content]').append(signatory_template);
    signatoryRestriction();
    $.ajax({

        type: 'POST',
        data: {
            id: idx,
        },
        url: 'OverTime/FetchSpecificOvertime',
        dataType: 'json'

    }).done(function (result) {
        form_id = result['id'];
        for_cancellation = result['is_deleted'];
        $('div[name=modal_overtime]').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
        $('.modal-footer').empty();



        $('input[name=ot_date_filed]').val(result['date_requested']);
        $('input[name=ot_empname]').val(result['empname']);
        $('input[name=ot_company]').val(result['compname']);
        $('select[name=overtime_type]').val(result['ot_refno']).attr('disabled', true);

        $('input[name=overtime_worksched_datein]').val(result['workshed_datein']).attr('disabled', true);
        $('input[name=overtime_worksched_timein]').val(result['workshed_timein']).attr('disabled', true);
        $('input[name=overtime_worksched_dateout]').val(result['worksched_dateout']).attr('disabled', true);
        $('input[name=overtime_worksched_out]').val(result['worksched_timeout']).attr('disabled', true);

        $('input[name=overtime_actual_datein]').val(result['actual_datein']).attr('disabled', true);
        $('input[name=overtime_actualin]').val(result['actual_timein']).attr('disabled', true);
        $('input[name=overtime_actual_dateout]').val(result['actual_dateout']).attr('disabled', true);
        $('input[name=overtime_actualout]').val(result['actual_timeout']).attr('disabled', true);
        $('textarea[name=overtime_reason]').val(result['ot_reason']).attr('disabled', true);

        if ($('select[name=overtime_type]').val() == 'REST DAY OVERTIME') {
            $('input[name=overtime_excess_from]').val(result['excess_rdot_timein']);
            $('input[name=overtime_excess_to]').val(result['excess_rdot_timeout']);
            $('input[name=overtime_excess_total]').val(result['total_excess']);
            $('div[name=excess_rdot]').removeClass('hidden');
        } else {
            $('div[name=excess_rdot]').addClass('hidden');
        }

        $('.modal-footer').addClass('hidden');




        userApproval(result, head, hr, supervisor, 3);

    });

}


function signatoryRestriction() {
    $('a[name=leave_signatory]').addClass('hidden');
    $('a[name=undertime_signatory]').addClass('hidden');
    $('a[name=overtime_signatory]').addClass('hidden');
    $('a[name=cs_signatory]').addClass('hidden');
    $('div[name=upon_request_content]').addClass('hidden');
    $('table[name=upon_request_title]').addClass('hidden');
    $('div[name=cancellation_content]').addClass('hidden');
    $('table[name=cancellation_title]').addClass('hidden');

    if (tab_category == 0) {
        $('div[name=upon_request_content]').removeClass('hidden');
    } else {
        $('div[name=cancellation_content]').removeClass('hidden');
    }
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