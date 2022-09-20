/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var select_under = new Array();
var structure_holder = new Array();
var tags = '';
var is_first = 0;

select_under[0] = 'request_company';
select_under[1] = 'request_location';
select_under[2] = 'request_division';
select_under[3] = 'request_department';
select_under[4] = 'request_section';
select_under[5] = 'request_area';

structure_holder[0] = 'company_div';
structure_holder[1] = 'location_div';
structure_holder[2] = 'division_div';
structure_holder[3] = 'department_div';
structure_holder[4] = 'section_div';
structure_holder[5] = 'area_div';
var structure = 0;
$(function () {

});
function FetchRole() {
    var dfrd1 = $.Deferred();
    setTimeout(function () {
        dfrd1.resolve();
        $.ajax({
            type: 'POST',
            url: 'Structure/SetupStructure',
            dataType: 'json'
        }).done(function (result) {

            for (var index = 0; index < select_under.length; index++) {
                  $('#' + select_under[index]).append('<option value="All">All</option>');
                $('#' + select_under[index]).append(result[index]);
            }
        });
    }, 1000);
    return $.when(dfrd1).done().promise();
}





function returnArrayStructure(holder) {
    var data = {};
    var structure = ['comID', 'locID', 'divID', 'depID', 'secID', 'areID'];

    for (var cv = 0; cv < 6; cv++) {
        if ($('#' + holder[cv]).val() != '' && $('#' + holder[cv]).val() != null) {
            data[structure[cv]] = $('#' + holder[cv]).val();
        }

    }
    return data;
}


function disableStructure(flag) {
    $('#request_form_under').attr('disabled', flag);
    $('#request_company').attr('disabled', flag);
    $('#request_location').attr('disabled', flag);
    $('#request_division').attr('disabled', flag);
    $('#request_department').attr('disabled', flag);
    $('#request_section').attr('disabled', flag);
    $('#request_area').attr('disabled', flag);
    $('#datefiled_in').attr('disabled', flag);
}