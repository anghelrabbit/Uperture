/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var tab_category = 0;
var change_schedule_table = null;
var signatory_template = $('#signatory-template').html();
$(function () {
    setupPayPeriod().done(function () {
        tabCategory('');
        by_cutoff = 1;
    });
});


function tabCategory(index) {
    fetchRequestors();
}
function fetchRequestors() {
    $('#requester_table').DataTable().clear().destroy();
    change_schedule_table = $('#requester_table').DataTable({
        dom: 'frtip',
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],
        ajax: {
            url: 'Reliever/FetchMyRequester',
            type: 'POST',
            data: {
                datein: $('#worksched_in').val(),
                dateout: $('#worksched_out').val()
            },

        },

    });
    $('#requester_table_filter').empty();
}

function changescheduleModal(supervisor, head, hr, reliever, id) {
    $('div[name=cs_approval_content]').empty();
    $('div[name=cs_approval_content]').append(signatory_template);
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
        $('div[name=upon_request_content]').removeClass('hidden');
        $('div[name=cancellation_content]').addClass('hidden');
        $('table[name=upon_request_title]').addClass('hidden');



    });
}