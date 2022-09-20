/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var table_holiday = null;
var holiday_id = 0;
$(function () {
    holidayTable();
    $("#datepicker_smaple").datepicker({
        format: "mm-yyyy",
        startView: "months",
        minViewMode: "months"
    });
    holidayTypes();
});



function holidayTable() {
    $('#holiday_table').DataTable().clear().destroy();
    table_holiday = $('#holiday_table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        "columnDefs": [{
                "targets": 0,
                "visible": true},
        ],

        ajax: {
            url: 'Holiday/FetchHolidays',
            type: 'POST',

            data: {
                holiday_year: $('input[name=holiday_year]').val(),
                holiday_month: $('select[name=holiday_month]').val()
            }

        }

    }
    );
    $('#holiday_table_length').empty();
    $('#holiday_table_filter').empty();
    $('#holiday_table_info').addClass('hidden');
}
var current_row = 0;




$('select[name=holiday_month]').on('change', function () {
    var search_holiday = $('select[name=holiday_month]').val() + "/" + $('input[name=holiday_year]').val();
    table_holiday
            .columns(0)
            .search(search_holiday)
            .draw();

});

function holidayTypes() {
    $.ajax({
        type: 'POST',
        url: "Holiday/FetchHolidayTypes",

        dataType: 'json'
    }).done(function (data) {
        $('select[name=holiday_type]').empty();
        for (var cv = 0; cv < data.length; cv++) {
            $('select[name=holiday_type]').append('<option value="' + data[cv]['refno'] + "," + data[cv]['incentive'] + '">' + data[cv]['incentive'] + '</option>');
        }

    });
}

$('input[name=holiday_year]').on('keyup', function () {
    var search_holiday = $('select[name=holiday_month]').val() + "/" + $('input[name=holiday_year]').val();
    table_holiday
            .columns(0)
            .search(search_holiday)
            .draw();
});



$('span[name=btn_holiday]').on('click', function () {
    $('div[name=add_holiday]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    $('input[name=holiday_date]').val('');
    $('input[name=holiday_name]').val('');

    $('label[name=holiday_date_error]').text('');
    $('label[name=holiday_name_error]').text('');
    $('label[name=holiday_date_error]').empty();
    $('label[name=holiday_name_error]').empty();
    holiday_id = 0;

});


$('button[name=save_holiday]').on('click', function () {
    $('div[name=loading_modal]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    $.ajax({
        type: 'POST',
        url: "Holiday/AddHoliday",
        data: $('#holiday_form').serialize() + '&id=' + holiday_id,
        dataType: 'json'
    }).done(function (data) {
        $('div[name=loading_modal]').modal('hide');
        if (data['success'] === false) {
            $.each(data['messages'], function (index, value) {
                if (value != '') {
                    $('label[name=' + index + '_error]').removeClass('hidden');
                    $('label[name=' + index + '_error]').empty('');
                    $('label[name=' + index + '_error]').append(value);
                }
            });
        } else {
            swal({title: "Success",
                text: "Holiday saved.",
                type: "success",
                show: true,
                backdrop: 'static',
                timer: 1600,
                showConfirmButton: false,
                keyboard: false},
                    function ()
                    {
                        $('div[name=add_holiday]').modal('hide');
                        swal.close();
                        holidayTable();
                    });
        }
    });
});


function editHoliday(index) {
    $.ajax({
        type: 'POST',
        url: "Holiday/FetchHoliday",
        data: {id: index},
        dataType: 'json'
    }).done(function (data) {
        if (data) {
            $('div[name=add_holiday]').modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
            holiday_id = data['ID'];
            $('label[name=holiday_date_error]').empty();
            $('label[name=holiday_name_error]').empty();
            $('input[name=holiday_date]').val(data['datex']);
            $('input[name=holiday_name]').val(data['description']);
            $('select[name=holiday_type]').val(data['refno'] + "," + data['type']);
        }
    });
}

function removeHoliday(index) {
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
            url: "Holiday/RemoveHoliday",
            data: {id: index},
            dataType: 'json'
        }).done(function (data) {
            swal({title: "Success",
                text: "Holiday removed.",
                type: "success",
                show: true,
                backdrop: 'static',
                timer: 1600,
                showConfirmButton: false,
                keyboard: false},
                    function ()
                    {
                        swal.close();
                        holidayTable();
                    });
        });
    });
}

$('input[name=year_selected]').on('keyup', function () {
    console.log('hererere');
    $('label[name=year_pasted]').text('');
    $('label[name=year_pasted]').append($('input[name=year_selected]').val());

});
$('input[name=year_based]').on('keyup', function () {
    $('label[name=year_copied]').text('');
    $('label[name=year_copied]').append($('input[name=year_based]').val());

});

$('span[name=btn_import_holiday]').on('click', function () {
    $('div[name=modal_merge_holiday]').modal('show');
    $('label[name=year_pasted]').text('');
    $('label[name=year_copied]').text('');
    $('label[name=year_pasted]').append($('input[name=year_selected]').val());
    $('label[name=year_copied]').append($('input[name=year_based]').val());
});

$('button[name=merge_holiday]').click(function () {
    $('div[name=loading_modal]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    $.ajax({
        type: 'POST',
        url: "Holiday/MergeHolidays",
        data: {yearin: $('input[name=year_based]').val(), yearof: $('input[name=year_selected]').val()},
        dataType: 'json'
    }).done(function (data) {
        if (data > 0) {
            $('div[name=loading_modal]').modal('hide');
            swal({title: "Success",
                text: $('input[name=year_based]').val() + ' holidays merged in ' + $('input[name=year_selected]').val(),
                type: "success",
                show: true,
                backdrop: 'static',
                timer: 2000,
                showConfirmButton: false,
                keyboard: false},
                    function ()
                    {
                        $('div[name=modal_merge_holiday]').modal('hide');
                        holidayTable();
                        swal.close();
                    });
        }
    });
});