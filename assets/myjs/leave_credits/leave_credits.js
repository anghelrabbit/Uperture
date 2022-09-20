/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var credits_table = null;
var structure_template = $('#hidden-template').html();
var leavetype_template = $('script[name=leavetype_template]').html();
var page = 0;
var leavetypes = {};
$(function () {
    $('div[name=struct_holder]').append(structure_template);
    FetchRole().done(function () {
//        tabCategory(0);
        fetchLeaveTypes();
    });
});
function fetchLeaveTypes() {
    if (credits_table != null) {
        credits_table.clear().destroy();
    }
    $('table tr[name=leave_credit_table_tr]').empty();
    $('table tr[name=leave_credit_table_tr]').append('<th ></th><th >Employee</th>');

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'LeaveCredits/FetchLeaveTypes',
        data: {category: 1}
    }).done(function (result) {
        leavetypes = result;
        for (var cv = 0; cv <= result.length; cv++) {
            if (cv == result.length) {
                leavecreditsTable();
            } else {
                $('table tr[name=leave_credit_table_tr]').append('<th >' + result[cv]['name'] + '</th>');
            }
        }
    });
}

function leavecreditsTable() {
    credits_table = $('#employee_leavecredits_table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            data: {structure: JSON.stringify(returnArrayStructure(select_under)),
                leavetypes: JSON.stringify(leavetypes)},
            url: "LeaveCredits/FetchEmployeeCredits",
            type: "POST",
        },
    });
    $('#employee_leavecredits_table_filter').empty();
    $('#employee_leavecredits_table_length').empty();
}


function tabCategory(category) {
    if (category != '') {
        page = category;
    }
    if (page == 0) {
        fetchLeaveTypes();
    } else {
        fetchEmployees(0);
    }
}


function updateCredit(profileno) {

}


$('span[name=btn_addcredits]').click(function () {
    $('div[name=leavetype_container]').empty();
    $('div[name=add_leavetype_table_container]').empty();
    $('div[name=emp_leavetype_container]').empty();
    $('div[name=add_leavetype_table_container]').append(leavetype_template);
    id = 0;
    $('div[name=struct_holder]').empty();
    $('div[name=modal_struct_holder]').append(structure_template);
    $('div[name=modal_givecredits]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    FetchRole().done(function () {
        tabCategory(1);
    });
    leaveTypeTable(0, '');
});
$('span[name=btn_addleavetype]').click(function () {
    $('div[name=leavetype_container]').empty();
    $('div[name=add_leavetype_table_container]').empty();
    $('div[name=emp_leavetype_container]').empty();
    $('div[name=leavetype_container]').append(leavetype_template);
    $('div[name=modal_add_leavetype]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    leaveTypeTable(1, '');

});

function openUpdateCredits(profileno) {
    $('div[name=leavetype_container]').empty();
    $('div[name=add_leavetype_table_container]').empty();
    $('div[name=emp_leavetype_container]').empty();
    $('div[name=emp_leavetype_container]').append(leavetype_template);
    $('div[name=modal_update_credits]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    leaveTypeTable(2, profileno);
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'Employee/FetchEmployee',
        data: {profileno: profileno}
    }).done(function (result) {
        $('h3[name=profile_name]').empty();
        $('h3[name=profile_name]').append(result['name']);
        $('h5[name=profile_job]').empty();
        $('h5[name=profile_job]').append(result['job']);
    });
}

