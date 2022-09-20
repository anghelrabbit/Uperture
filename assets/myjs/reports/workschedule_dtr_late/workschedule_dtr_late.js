/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var emp_data = new Array();
$(function () {
    FetchRole().done(function () {
        setupPayPeriod().done(function () {
            fetchScheduleSummary();
        });
    });
});
function tabCategory(category) {
    fetchScheduleSummary();
}



function fetchScheduleSummary() {
    $('#schedule_summary_table').DataTable().clear().destroy();
    $('#schedule_summary_table').DataTable({
        dom: 'frtip',
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],
        ajax: {
            url: 'Workschedule/ScheduleSummary',
            type: 'POST',
            data: {
                structure: JSON.stringify(returnArrayStructure(select_under)),
                datein: $('#worksched_in').val(),
                dateout: $('#worksched_out').val(),
            },

        },

    });
    $('#schedule_summary_table_filter').addClass('hidden');
}


function generateReport() {
    $('div[name=loading_modal]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'WorkSchedule/GenerateEmployeeSchedule',
        data: {
            structure: JSON.stringify(returnArrayStructure(select_under)),
            worksched_in: $('#worksched_in').val(),
            worksched_out: $('#worksched_out').val(),
        }
    }).done(function (result) {
        console.log(result);
        $('div[name=loading_modal]').modal('hide');
        $('div[name=modal_report]').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
        emp_data = result;
    });
}

$('span[name=generate_report_modal]').click(function () {
    generateReport();
});
$('span[name=pdf_form]').click(function () {
    $('input[name=structure]').val(JSON.stringify(returnArrayStructure(select_under)));
    $('input[name=worksched_in]').val($('#worksched_in').val());
    $('input[name=worksched_out]').val($('#worksched_out').val());
    $('input[name=emp_data]').val(JSON.stringify(emp_data));
    $('input[name=category]').val($('select[name=report_category]').val());
    $('input[name=is_pdf]').val("1");
    $('form[name=generate_report]').submit();
});
$('span[name=excel_form]').click(function () {
    $('input[name=structure]').val(JSON.stringify(returnArrayStructure(select_under)));
    $('input[name=worksched_in]').val($('#worksched_in').val());
    $('input[name=worksched_out]').val($('#worksched_out').val());
    $('input[name=emp_data]').val(JSON.stringify(emp_data));
    $('input[name=category]').val($('select[name=report_category]').val());
    $('input[name=is_pdf]').val("0");
    $('form[name=generate_report]').submit();
});