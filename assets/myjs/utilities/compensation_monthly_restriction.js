var restrict_table = null;
var emp_restrict = {};
$('span[name=btn_month_restrict]').click(function () {
    $('div[name=modal_monthly_restriction]').modal('show');
    employeeRestricTable();
    emp_restrict = {};
});
$('select[name=onemonth_restrict]').change(function () {
    employeeRestricTable();
});
function employeeRestricTable() {
    var columndef = [];
    if ($('select[name=onemonth_restrict]').val() != 0) {
    columndef = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        var month_name = $('select[name=onemonth_restrict]').val().split('+');
        columndef = $.grep(columndef, function (value) {
            return value != month_name[0];
        });
    }
    $('#emp_monthly_restrict').DataTable().clear().destroy();
    restrict_table = $('#emp_monthly_restrict').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        "columnDefs": [
            {
                "targets": columndef,
                "visible": false},
        ],
        ajax: {
            data: {
                category: 0,
                selected_department: JSON.stringify(selected_departments),
                unselected_department: JSON.stringify(unselected_departments),
                selected_profilen: JSON.stringify(selected_profileno),
                unselected_profilen: JSON.stringify(unselected_profileno),
                emp_restricts: JSON.stringify(emp_restrict),
            },
            url: 'MonthlyRestriction/EmployeeRestrict',
            type: 'POST',
            complete: function () {

            }

        },
        createdRow: function (row, data, dataIndex)
        {

        }

    });
    $('#emp_monthly_restrict_length').addClass('hidden');
    $('#emp_monthly_restrict_filter').addClass('hidden');
}

$('input[name=restrict_firstname]').on('keyup', function () {
    var lastname = '';
    if ($('input[name=restrict_firstname]').val() != '') {
        lastname = "/lastname/" + $('input[name=restrict_lastname]').val();
    }

    restrict_table
            .columns(1)
            .search('firstname/' + this.value + lastname + '/' + JSON.stringify(emp_restrict))
            .draw();
});
$('input[name=restrict_lastname]').on('keyup', function () {
    var firstname = '';
    if ($('input[name=restrict_firstname]').val() != '') {
        firstname = "/firstname/" + $('input[name=restrict_firstname]').val();
    }
    restrict_table
            .columns(1)
            .search('lastname/' + this.value + firstname + '/' + JSON.stringify(emp_restrict))
            .draw();
});

function restrictEmployee(element, profileno, month) {
    if (!$(element).is(':checked')) {
        if (profileno in emp_restrict) {
            emp_restrict[profileno][month] = month;
        } else {
            emp_restrict[profileno] = {};
            emp_restrict[profileno][month] = month;
        }
    } else {
        delete emp_restrict[profileno][month];
    }
}