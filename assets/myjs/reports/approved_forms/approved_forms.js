/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var leave_table = '';
var undertime_table = '';
var overtime_table = '';
var cs_table = '';
var category_form = 0;
$(function () {
      $('input[name=cut_off_toggle]').bootstrapToggle();
    FetchRole().done(function () {
        setupPayPeriod().done(function () {
            tabCategory(0);
        });
    });
 $.ajax({
        type: "POST",
        data: {
        },
        dataType: "json",
        url: "Overtime/SetupOvertimeTypes",
    }).done(function (result) {
        $('select[name=search_overtime_type]').empty();
            $('select[name=search_overtime_type]').append('<option value="">All</option>');
        $.each(result, function (key, value) {
            $('select[name=search_overtime_type]').append('<option value="' + value + '">' + key + '</option>');
        });
    });
    
});

function tabCategory(category) {

    if (category !== '') {
        category_form = category;
    }
    if (category_form == 0) {
        approved_leave();
    } else if (category_form == 1) {
        approved_undertime();
    } else if (category_form == 2) {
        approved_cs();
    } else if (category_form == 3) {
        approved_overtime();
    }
}

function approved_leave() {
    $('#approved_leave_table').DataTable().clear().destroy();
    leave_table = $('#approved_leave_table').DataTable
            ({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                processing: true,
                serverSide: true,

                "columnDefs": [
                    {
                        "targets": [0, 1, 2, 4, 3, 5, 6],
                        "orderable": false
                    },
                ],
                ajax: {
                    url: "Leave/FetchLeaveForms",
                    data: {
                        structure: JSON.stringify(returnArrayStructure(select_under)),
                        under: $('#request_form_under').val(),
                        datefiledin: $('#worksched_in').val(),
                        datefiledout: $('#worksched_out').val(),
                        page: 0,
                        tab_category: '',
                    },
                    type: "POST",
                    complete: function (data) {

                    }
                },

            });
    $('#approved_leave_table_filter').addClass('hidden');

}

function approved_cs(){
      $('#approved_cs_table').DataTable().clear().destroy();
    cs_table = $('#approved_cs_table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],
        ajax: {
            data: {
                structure: JSON.stringify(returnArrayStructure(select_under)),
                under: $('#request_form_under').val(),
                page: 0,
                datefiledin: $('#worksched_in').val(),
                datefiledout: $('#worksched_out').val(),
                tab_category: ''
            },
            url: 'ChangeSchedule/FetchRequestedSchedule',
            type: 'POST'

        },

    });
    $('#approved_cs_table_filter').addClass('hidden');
}

function approved_undertime() {
    $('#approved_undertime_table').DataTable().clear().destroy();
    undertime_table = $('#approved_undertime_table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],
        ajax: {
            data: {
                structure: JSON.stringify(returnArrayStructure(select_under)),
                under: $('#request_form_under').val(),
                page: 0,
                datefiledin: $('#worksched_in').val(),
                datefiledout: $('#worksched_out').val(),
                tab_category: ''
            },
            url: 'Undertime/FetchUndertimeForms',
            type: 'POST'

        },

    });
    $('#approved_undertime_table_filter').addClass('hidden');
}

function approved_overtime() {
    $('#approved_overtime_table').DataTable().clear().destroy();
    overtime_table = $('#approved_overtime_table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],
        ajax: {
            data: {
                structure: JSON.stringify(returnArrayStructure(select_under)),
                under: $('#request_form_under').val(),
                page: 0,
                datefiledin: $('#worksched_in').val(),
                datefiledout: $('#worksched_out').val(),
                tab_category: ''
            },
            url: 'Overtime/FetchRequestedOvertime',
            type: 'POST'

        },

    });
    $('#approved_overtime_table_filter').addClass('hidden');
}



$('input[name=leave_employeename]').on('keyup', function () {
    leave_table
            .columns(4)
            .search(this.value)
            .draw();
});
$('select[name=leave_type_search]').on('change', function () {
    leave_table
            .columns(5)
            .search(this.value)
            .draw();
});
$('input[name=undertime_employeename]').on('keyup', function () {
    undertime_table
            .columns(4)
            .search(this.value)
            .draw();
});
$('select[name=search_undertime_type]').on('change', function () {
    undertime_table
            .columns(5)
            .search(this.value)
            .draw();
});
$('input[name=overtime_employeename]').on('keyup', function () {
    overtime_table
            .columns(4)
            .search(this.value)
            .draw();
});
$('select[name=search_overtime_type]').on('change', function () {
    overtime_table
            .columns(5)
            .search(this.value)
            .draw();
});
$('input[name=cs_employeename]').on('keyup', function () {
    cs_table
            .columns(4)
            .search(this.value)
            .draw();
});
$('select[name=cs_type_search]').on('change', function () {
    cs_table
            .columns(5)
            .search(this.value)
            .draw();
});
$('input[name=undertime_employeename]').on('click', function (e) {
    e.stopPropagation();
});
$('select[name=search_undertime_type]').on('click', function (e) {
    e.stopPropagation();
});
$('input[name=overtime_employeename]').on('click', function (e) {
    e.stopPropagation();
});
$('select[name=search_overtime_type]').on('click', function (e) {
    e.stopPropagation();
});
$('input[name=cs_employeename]').on('click', function (e) {
    e.stopPropagation();
});
$('select[name=cs_type_search]').on('click', function (e) {
    e.stopPropagation();
});



$('span[name=pdf_form]').on('click', function () {
    $('input[name=structure]').val( JSON.stringify(returnArrayStructure(select_under)));
    $('input[name=worksched_in]').val($('#worksched_in').val());
    $('input[name=worksched_out]').val($('#worksched_out').val());
    $('input[name=category]').val(category_form);
    $('form[name=generate_report]').submit();
});
$('span[name=excel_form]').on('click', function () {
    $('input[name=excel_structure]').val( JSON.stringify(returnArrayStructure(select_under)));
    $('input[name=excel_worksched_in]').val($('#worksched_in').val());
    $('input[name=excel_worksched_out]').val($('#worksched_out').val());
    $('input[name=excel_category]').val(category_form);
    $('form[name=generate_excel_report]').submit();
});
