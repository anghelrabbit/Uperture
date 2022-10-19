/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var emp201_table = null;

$(function () {
    FetchRole().done(function () {
        tabCategory();
    }
    );
});

function countAnimation() {
    $('.count').each(function () {
        var $this = $(this);
        jQuery({Counter: 0}).animate({Counter: $this.text()}, {
            duration: 1000,
            easing: 'swing',
            step: function () {
                $this.text(Math.ceil(this.Counter));
            }
        });
    });
}

function generateExcel201Report() {
    $('input[name=structure]').val(JSON.stringify(returnArrayStructure(select_under)));
    $('input[name=lastname]').val($('input[name=201_lastname]').val());
    $('input[name=firstname]').val($('input[name=201_firstname]').val());
    $('input[name=sex]').val($('select[name=201_gender]').val());
    $('input[name=years_service]').val(search_years_service());

    $('#generate_201_excel').submit();
}
function generatePDF201Report() {
    $('input[name=structure_pdf]').val(JSON.stringify(returnArrayStructure(select_under)));
    $('input[name=lastname_pdf]').val($('input[name=201_lastname]').val());
    $('input[name=firstname_pdf]').val($('input[name=201_firstname]').val());
    $('input[name=sex_pdf]').val($('select[name=201_gender]').val());
    $('input[name=years_service_pdf]').val(search_years_service());

    $('#generate_201_report').submit();
}


function tabCategory() {
    countAllEmployees();
    FetchEmployee201();
}

function countAllEmployees() {
    disableStructure(true);
    $.ajax({
        type: 'POST',
        data: {
            structure: JSON.stringify(returnArrayStructure(select_under)),
            under: $('#request_form_under').val()
        },
        url: 'Employee/TotalEmployees',
        dataType: 'json'
    }).done(function (result) {
        disableStructure(false);
        $('#totalhremployees').empty();
        $('#totalretiredhremployees').empty();
        $('#totalhrmalesx').empty();
        $('#totalhrfemalesx').empty();
        $('#totalresignedhremployees').empty();
        $('#totalnewhiredhremployees').empty();

        $('#totalhremployees').append(result.total_emp);
        $('#totalretiredhremployees').append(result.retired_emp);
        $('#totalhrmalesx').append(result.male_emp);
        $('#totalhrfemalesx').append(result.female_emp);
        $('#totalresignedhremployees').append(result.resign_emp);
        $('#totalnewhiredhremployees').append(result.new_hired);

        countAnimation();

    });
}

function FetchEmployee201() {
    $('#employee_201_table').DataTable().clear().destroy();
    emp201_table = $('#employee_201_table').DataTable
            ({
                responsive: true,
                processing: true,
                serverSide: true,
                "columnDefs": [{
                        "targets": 0,
                        "visible": false,
                        "orderable": false
                    },
                    {
                        "targets": 1,
                        "orderable": false
                    },

                    {
                        "targets": 7,
                        "orderable": false
                    }, {
                        "targets": 8,
                        "orderable": false
                    },
                    {
                        "targets": 9,
                        "orderable": false
                    },
                ],

                ajax:
                        {
                            url: "Emp201File/EmployeeRecord",
                            type: "POST",
                            data: {
                                structure: JSON.stringify(returnArrayStructure(select_under)),
                                under: $('#request_form_under').val(),
                            },
                        },

                initComplete: function (settings, json)
                {

                }
            });
    $('#employee-recordx-table_filter').addClass('hidden');
    $('#employee_201_table_filter').empty();


    $('#employee_201_table_filter').append('<span  class="btn btn-fw " style="background-color:#3ED03E;color:white"  onclick="generatePDF201Report()"><b>PDF File</b></span>');
    $('#employee_201_table_filter').append('  <span  class="btn btn-fw " style="background-color:#3ED03E;color:white"  onclick="generateExcel201Report()"><b>Excel File</b></span>');

}
$('input[name=201_firstname]').on('keyup', function () {
    var lastname = '';
    if ($('input[name=201_firstname]').val() != '') {
        lastname = "-lastname-" + $('input[name=201_lastname]').val();
    }
    emp201_table
            .columns(4)
            .search('firstname-' + this.value + lastname)
            .draw();
});
$('input[name=201_lastname]').on('keyup', function () {
    var firstname = '';
    if ($('input[name=201_firstname]').val() != '') {
        firstname = "-firstname-" + $('input[name=201_firstname]').val();
    }
    emp201_table
            .columns(4)
            .search('lastname-' + this.value + firstname)
            .draw();
});
$('select[name=201_gender]').on('change', function () {
    emp201_table
            .columns(7)
            .search(this.value)
            .draw();
});

$('input[name=201_years]').on('keyup', function () {

    emp201_table
            .columns(6)
            .search(search_years_service())
            .draw();
});
$('input[name=201_months]').on('keyup', function () {

    emp201_table
            .columns(6)
            .search(search_years_service())
            .draw();
});


function search_years_service() {
    var years = 0;
    var months = 0;
    if ($('input[name=201_years]').val() != '') {
        years = $('input[name=201_years]').val();
    }
    if ($('input[name=201_months]').val() != '') {
        months = $('input[name=201_months]').val();
    }
    return years + "-" + months;

}
var uname = '';
var pass = '';


function retrieveAccount(profileno) {
    uname = '';
    pass = '';
   
    $.ajax({
        type: 'POST',
        data: {
            profileno: profileno
        },
        url: 'Emp201File/FetchSpecificEmployee',
        dataType: 'json'
    }).done(function (result) {
//        $('h3[name=profile_name]').empty();
//        $('h5[name=profile_dept]').empty();
//        $('img[name=image_profile]').attr("src", "data:image;base64," + result['profile_pic']);
//   
//        $('h3[name=profile_name]').append(result['lastname'] + ", " + result['firstname']);
//        $('h5[name=profile_dept]').append(result['department'][0]['name']);
//        uname = result['username'];
//        pass = result['password'];
//        
        $('div[name=modal_account]').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
    });
}

function directToUserProfile(profileno) {
    $('input[name=profileno]').val(profileno);
    $('form[name=profile_form]').submit();
}
var holderEnter = 0;
function enable_enterkey() {
    holderEnter = 0;
}
function check_profile_account() {
    $.ajax
            ({
                type: 'POST',
                url: "LandingPage/checkPassword",
                data: {
                    password: $('input[name=profile_password]').val(),
                },
                dataType: 'json'
            })
            .done(function (data)
            {
                if (data) {
                    $('div[name=lock_overlay]').addClass('hidden');
                    $('span[name=profile_username]').empty();
                    $('span[name=profilen_password]').empty();
                    $('span[name=profile_username]').append(uname);
                    $('span[name=profilen_password]').append(pass);
                }
            });
}
function profile_account_keypress(e) {
    e = e || window.event;
    var key = e.keyCode;
    if (key == 13)
    {
        if (holderEnter == 0) {
            check_profile_account();
            holderEnter = 1;
        }
        return false;
    }
}



$('input[name=201_firstname]').on('click', function (e) {
    e.stopPropagation();
});
$('input[name=201_lastname]').on('click', function (e) {
    e.stopPropagation();
});
$('input[name=201_years]').on('click', function (e) {
    e.stopPropagation();
});
$('input[name=201_months]').on('click', function (e) {
    e.stopPropagation();
});


