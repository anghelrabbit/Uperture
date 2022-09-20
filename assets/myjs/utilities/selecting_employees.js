/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



var selected_departments = {};
var unselected_departments = {};
var selected_profileno = {};
var unselected_profileno = {};
var structure_profileno_selected = {};
var structure_profileno_unselected = {};
var employees_table = null;




function checkReferences() {
    var dept_string = '';
    var pointer = 0;
    for (var cv = 0; cv < select_under.length; cv++) {
        if ($('#' + select_under[cv]).val() == 'All') {
            pointer++;
        }
        if (dept_string == '') {
            dept_string = $('#' + select_under[cv]).val();
        } else {
            dept_string = dept_string + "/" + $('#' + select_under[cv]).val();

        }
    }
    if (pointer == 6) {
        unselected_departments = {};
        selected_profileno = {};
        unselected_profileno = {};
        selected_departments = {};
    } else {
        var dept_string_split = dept_string.split('/');
        checkProfileInDepartment(structure_profileno_selected, dept_string_split, selected_profileno);
        checkProfileInDepartment(structure_profileno_unselected, dept_string_split, unselected_profileno);
    }
    if ($('input[name=check_all]').is(':checked')) {
        if (dept_string in unselected_departments) {
            delete unselected_departments[dept_string];
        }
        selected_departments[dept_string] = dept_string;
    } else {
        if (pointer == 6) {
            unselected_departments = {};
            selected_profileno = {};
            unselected_profileno = {};
            selected_departments = {};
        } else {

            if (dept_string in selected_departments) {
                delete selected_departments[dept_string];
            }
            unselected_departments[dept_string] = dept_string;
        }

    }
    employees_table
            .columns(0)
            .search(JSON.stringify(selected_departments) + "+" +
                    JSON.stringify(unselected_departments) + "+" +
                    JSON.stringify(selected_profileno) + "+" +
                    JSON.stringify(unselected_profileno))
            .draw(false);

}
function checkProfileInDepartment(structure_profileno, dept_string_split, profileno_array) {
    $.each(structure_profileno, function (key, value) {
        var pointer = 0;
        var key_split = key.split('@');

        if (dept_string_split[0] == 'All' || dept_string_split[0] == key_split[0]) {
            pointer++;
        }
        if (dept_string_split[1] == 'All' || dept_string_split[1] == key_split[1]) {
            pointer++;
        }
        if (dept_string_split[2] == 'All' || dept_string_split[2] == key_split[2]) {
            pointer++;
        }
        if (dept_string_split[3] == 'All' || dept_string_split[3] == key_split[3]) {
            pointer++;
        }
        if (dept_string_split[4] == 'All' || dept_string_split[4] == key_split[4]) {
            pointer++;
        }
        if (dept_string_split[5] == 'All' || dept_string_split[5] == key_split[5]) {
            pointer++;
        }
        if (pointer == 6) {
            for (var cv = 0; cv < structure_profileno[key].length; cv++) {
                delete profileno_array[structure_profileno[key][cv]];
            }
            delete structure_profileno[key];

        }
    });
}




$('table[name=employees_table] tbody').on('click', 'tr', function () {
    var row_data = employees_table.row(this).data();
    var is_selected = row_data[1];
    var data_split = row_data[0].split('+');
    var trigger = 0;
    if (is_selected == 1) {
        row_data[1] = 0;
        employees_table.row(this).data(row_data);
        $('input[name=' + row_data[1] + "]").prop('checked', false);
        profilenoChecker(data_split, structure_profileno_unselected, structure_profileno_selected, unselected_profileno, selected_profileno);
        $(this).css({"background-color": "", "color": ""});

    } else if (is_selected == 0) {
        row_data[1] = 1;
        employees_table.row(this).data(row_data);
        $('input[name=' + data_split[1] + "]").prop('checked', true);
        profilenoChecker(data_split, structure_profileno_selected, structure_profileno_unselected, selected_profileno, unselected_profileno);
        $(this).css({"background-color": "#3EB3A3", "color": "white"});
    } else {
        trigger = 1;
    }
    if (trigger == 0) {
        employees_table
                .columns(0)
                .search(JSON.stringify(selected_departments) + "+" +
                        JSON.stringify(unselected_departments) + "+" +
                        JSON.stringify(selected_profileno) + "+" +
                        JSON.stringify(unselected_profileno))
                .draw(false);
    }
});

function profilenoChecker(data_split, structure_profileno1, structure_profileno2, profileno_holder, profileno_holder2) {


    profileno_holder[data_split[1]] = data_split[1];
    if (data_split[0] in structure_profileno1) {
        structure_profileno1[data_split[0]].push(data_split[1]);
    } else {
        structure_profileno1[data_split[0]] = [];
        structure_profileno1[data_split[0]].push(data_split[1]);
    }
    if (data_split[0] in structure_profileno2) {
        for (var cv = 0; cv < structure_profileno2[data_split[0]].length; cv++) {
            structure_profileno2[data_split[0]] = jQuery.grep(structure_profileno2[data_split[0]], function (value) {
                if (data_split[1] == value) {
                    delete profileno_holder2[data_split[1]];
                }
                return value != data_split[1];
            });
        }
        if (structure_profileno2[data_split[0]].length == 0) {
            delete structure_profileno2[data_split[0]];
        }
    }
}




function fetchEmployees(category) {

    $('input[name=check_all]').prop('checked', false);
    $('table[name=employees_table]').DataTable().clear().destroy();
    employees_table = $('table[name=employees_table]').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        "columnDefs": [
            {
                "targets": [0, 1],
                "visible": false
            },
            {
                "targets": [2, 5],
                "orderable": false
            },
            {
                "targets": 2,
                "width": "1%"
            },
            {
                "targets": 3,
                "width": "10%"
            },
            {
                "targets": 4,
                "width": "5%"
            },
            {
                "targets": 5,
                "width": "1px"
            }
        ],
        ajax: {
            data: {
                structure: JSON.stringify(returnArrayStructure(select_under)),
                service_years: $('#service_years').val(),
                service_months: $('#service_months').val(),
                category: category,
                selected_departments: JSON.stringify(selected_departments),
                unselected_department: JSON.stringify(unselected_departments),
                selected_profileno: JSON.stringify(selected_profileno),
                unselected_profileno: JSON.stringify(unselected_profileno)

            },
            url: 'Employee/FetchEmployeesToSelect',
            type: 'POST',
            complete: function () {

            }

        },
        createdRow: function (row, data, dataIndex)
        {
            if (data[1] == 1) {
                $(row).css('background-color', '#3EB3A3');
            } else {
                $(row).css('background-color', '');

            }
        }

    });

    $('#DataTables_Table_1_filter').empty();
    $('#DataTables_Table_0_filter').empty();
    $('#DataTables_Table_0_length').empty();
}







$('input[name=employees_firstname]').on('keyup', function () {
    var lastname = '';
    if ($('input[name=employees_firstname]').val() != '') {
        lastname = "/lastname/" + $('input[name=employees_lastname]').val();
    }
    employees_table
            .columns(1)
            .search('firstname/' + this.value + lastname)
            .draw();
});
$('input[name=employees_lastname]').on('keyup', function () {
    var firstname = '';
    if ($('input[name=employees_firstname]').val() != '') {
        firstname = "/firstname/" + $('input[name=employees_firstname]').val();
    }
    employees_table
            .columns(1)
            .search('lastname/' + this.value + firstname)
            .draw();
});

$('input[name=employees_years]').on('keyup', function () {

    employees_table
            .columns(3)
            .search(search_years_service())
            .draw();
});
$('input[name=employees_months]').on('keyup', function () {

    employees_table
            .columns(3)
            .search(search_years_service())
            .draw();
});


function search_years_service() {
    var years = 0;
    var months = 0;
    if ($('input[name=credits_years]').val() != '') {
        years = $('input[name=credits_years]').val();
    }
    if ($('input[name=employees_months]').val() != '') {
        months = $('input[name=employees_months]').val();
    }
    return years + "-" + months;

}


$('input[name=employees_firstname]').on('click', function (e) {
    e.stopPropagation();
});
$('input[name=employees_lastname]').on('click', function (e) {
    e.stopPropagation();
});
$('input[name=employees_years]').on('click', function (e) {
    e.stopPropagation();
});
$('input[name=employees_months]').on('click', function (e) {
    e.stopPropagation();
});
