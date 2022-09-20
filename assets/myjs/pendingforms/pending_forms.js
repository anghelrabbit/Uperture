/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var leave_table = '';
var undertime_table = '';
var overtime_table = '';
var change_schedule_table = '';
var category_form = 0;
var tab_category = 0;
var notif_tab_category =0;
var form_id = '';
var signatory_template = $('#signatory-template').html();
$(function () {
    $('span[name=leave_helpdesk]').addClass('hidden');
    $('span[name=undertime_helpdesk]').addClass('hidden');
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
        $('select[name=overtime_type]').empty();
        $('select[name=search_overtime_type]').empty();
        $('select[name=search_overtime_type]').append('<option value="">All</option>');
        $.each(result, function (key, value) {
            var split_value = value.split("+");
            $('select[name=overtime_type]').append('<option value="' + key + '">' + key + '</option>');
            $('select[name=search_overtime_type]').append('<option value="' + key + '">' + key + '</option>');
        });
    });

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'LeaveCredits/FetchTypeOfLeave'
    }).done(function (result) {
        $('select[name=leave_type_search]').empty();
        $('select[name=leave_type]').empty();
        $('select[name=leave_type_search]').append('<option value="">All</option>');
        $.each(result, function (key, value) {
            $('select[name=leave_type]').append('<option value="' + value['name']+"/"+value['id'] + '">' + value['name'] + '</option>');
            $('select[name=leave_type_search]').append('<option value="' + value['id'] + '">' + value['name'] + '</option>');
        });
    });
});

function tabCategory(category) {
    if (category !== '') {
        category_form = category;
    }
    if (category_form == 0) {
        requested_leave();
    } else if (category_form == 1) {
        requested_undertime();
    } else if (category_form == 2) {
        requested_change_sched();
    } else if (category_form == 3) {
        requested_overtime();
    }
}

function requested_leave() {
    $('table[name=leave_table]').DataTable().clear().destroy();
    leave_table = $('table[name=leave_table]').DataTable
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
                        "targets": [1],
                        "visible": false,
                    }, {
                        "targets": [1, 3, 5, 6, 7],
                        "orderable": false
                    },
                ],
                ajax: {
                    url: "Leave/FetchLeaveForms",
                    data: {
                        structure: JSON.stringify(returnArrayStructure(select_under)),
                        datefiledin: $('#worksched_in').val(),
                        datefiledout: $('#worksched_out').val(),
                        page: 1,
                        tab_category: tab_category,
                    },
                    type: "POST",
                    complete: function (data) {

                    }
                },

                createdRow: function (row, data, dataIndex)
                {
                    if (data[1] == 1) {
                        $(row).css('background-color', 'orange');
                    } else if (data[1] == 2) {
                        $(row).css('background-color', '#ff8785');
                    }
                }
            });
    $('#DataTables_Table_0_filter').empty();
}


function requested_undertime() {
    $('table[name=requested_undertime_table]').DataTable().clear().destroy();
    undertime_table = $('table[name=requested_undertime_table]').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],
        "columnDefs": [
            {"visible": false, "targets": 1}
        ],
        ajax: {
            data: {
                structure: JSON.stringify(returnArrayStructure(select_under)),
                page: 1,
                datefiledin: $('#worksched_in').val(),
                datefiledout: $('#worksched_out').val(),
                tab_category: tab_category
            },
            url: 'Undertime/FetchUndertimeForms',
            type: 'POST'

        },
        createdRow: function (row, data, dataIndex)
        {
            if (data[1] == 1) {
                $(row).css('background-color', 'orange');
            } else if (data[1] == 2) {
                $(row).css('background-color', '#ff8785');
            }
        }

    });
    $('#DataTables_Table_1_filter').addClass('hidden');

}
function requested_overtime() {
    data = returnArrayStructure(select_under);
    $('table[name=requested_overtime_table]').DataTable().clear().destroy();
    overtime_table = $('table[name=requested_overtime_table]').DataTable({
        dom: 'frtip',
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],
        "columnDefs": [{
                "targets": 1,
                "visible": false,
                "orderable": false
            }, {"targets": 0, "width": "5px"}

        ],
        ajax: {
            data: {
                structure: JSON.stringify(data),
                datefiledin: $('#worksched_in').val(),
                datefiledout: $('#worksched_out').val(),
                page: 1,
                tab_category: tab_category,
            },
            url: 'Overtime/FetchRequestedOvertime',
            type: 'POST'

        },

    });
}


function requested_change_sched() {
    $('table[name=change_schedule_table]').DataTable().clear().destroy();
    change_schedule_table = $('table[name=change_schedule_table]').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],
        "columnDefs": [
            {"visible": false, "targets": 1}
        ],
        ajax: {
            data: {
                data: JSON.stringify(returnArrayStructure(select_under)),
                page: 1,
                datefiledin: $('#worksched_in').val(),
                datefiledout: $('#worksched_out').val(),
                tab_category: tab_category
            },
            url: 'ChangeSchedule/FetchRequestedSchedule',
            type: 'POST'

        },
        createdRow: function (row, data, dataIndex)
        {
            if (data[1] == 1) {
                $(row).css('background-color', 'orange');
            } else if (data[1] == 2) {
                $(row).css('background-color', '#ff8785');
            }
        }

    });
    $('#change_schedule_table_filter').addClass('hidden');
}



$('li[name=filed_forms]').click(function () {
    tabClicks(0);
});
$('li[name=filed_cancel]').click(function () {
    tabClicks(1);
});


function tabClicks(category) {
    tab_category = category;
    notif_tab_category = category;
    $('button[name=category_button]').empty();
    countPendingForms(category);
    if (category == 0) {
        $('a[href="#pendingchangesched"]').removeClass('hidden');
        $('thead').removeAttr('style', 'background-color:#ff8785; color:white');
        $('thead').attr('style', 'background-color:#2692D0; color:white');
        $('button[name=category_button]').append('Filed Forms &nbsp;<span class="fa fa-caret-down"></span>');
        $('button[name=category_button]').removeClass('btn btn-danger');
        $('button[name=category_button]').addClass('btn btn-primary');

        $('div[name=dashboard_box]').addClass('box-primary');
        $('div[name=dashboard_box]').removeClass('box-danger');
    } else {
        $('a[href="#pendingchangesched"]').addClass('hidden');
        $('thead').removeAttr('style', 'background-color:#2692D0;color:white');
        $('thead').attr('style', 'background-color:#ff8785;color:white');
        $('button[name=category_button]').append('Cancellation &nbsp;<span class="fa fa-caret-down"></span>');
        $('button[name=category_button]').removeClass('btn btn-primary');
        $('button[name=category_button]').addClass('btn btn-danger');

        $('div[name=dashboard_box]').removeClass('box-primary');
        $('div[name=dashboard_box]').addClass('box-danger');
        if (category_form == 2) {
            $('a[href="#pendingleave"]').click();
        } else {
            tabCategory(category_form);
        }
    }
}

function tabClick(category) {

    tab_category = category;
    if (category == 0) {
        $('#cancel_forms').removeAttr('style');

        $('#dashboard_box').addClass('box-primary');
        $('#dashboard_box').removeClass('box-danger');
        $('#table_tabs').removeClass('tab-danger');




    } else {
        $('#dashboard_box').removeClass('box-primary');
        $('#dashboard_box').addClass('box-danger');

        $('#cancel_forms').attr('style', 'background-color:#ff8785');
        $('#table_tabs').addClass('tab-danger');




    }

    tabCategory(category_form);
}

$('input[name=leave_employeename]').on('keyup', function () {
    leave_table
            .columns(4)
            .search(this.value)
            .draw();
});
$('input[name=cs_employeename]').on('keyup', function () {
    change_schedule_table
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
$('select[name=cs_type_search]').on('change', function () {
    change_schedule_table
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
$('input[name=leave_employeename]').on('click', function (e) {
    e.stopPropagation();
});
$('input[name=undertime_employeename]').on('click', function (e) {
    e.stopPropagation();
});
$('input[name=cs_employeename]').on('click', function (e) {
    e.stopPropagation();
});
$('select[name=cs_type_search]').on('click', function (e) {
    e.stopPropagation();
});
$('select[name=search_undertime_type]').on('click', function (e) {
    e.stopPropagation();
});
$('select[name=overtime_employeename]').on('click', function (e) {
    e.stopPropagation();
});
$('select[name=search_overtime_type]').on('click', function (e) {
    e.stopPropagation();
});
