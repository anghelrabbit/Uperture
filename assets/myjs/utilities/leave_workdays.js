/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var workDays = {};

function  dateRestrictions(data, day) {
    workDays = data[0];
    $('#divDates').empty();
    var total = 0;
    var startDate = new Date($('input[name=leave_from]').val());
    var endDate = new Date($('input[name=leave_to]').val());
    var monthChange = startDate.getMonth();
    var dayChange = startDate.getDate();
    var diff = new Date(endDate - startDate);
    diff = (diff / 1000 / 60 / 60 / 24) + 1;
    if (diff > 0) {
        while (diff !== 0) {
            var dateHandler = new Date(startDate.getFullYear(), monthChange, dayChange);
            var dayOfMonth = dateHandler.getDate();
            if (dayOfMonth < 10) {
                dayOfMonth = "0" + dayOfMonth;
            }
            total += appendToDivDates(dateHandler.getMonth(), dayOfMonth, dateHandler.getFullYear(), day);
            if (dayChange === new Date(dateHandler.getFullYear(), monthChange, 0).getDate()) {
                dayChange = 1;
                monthChange++;
            } else {
                dayChange++;
            }
            diff--;
        }
        $('#totalHours').val(total);
        $('#totalHours2').val(total);
    }

}

function appendToDivDates(thisMonth, day, year, val) {
    var month = parseInt(thisMonth) + 1;
    if (month < 10) {
        month = "0" + month;
    }
    var value = '';
    if (val == 0.5) {
        value = parseFloat(0.5);
    } else if (val == 1) {
        value = parseFloat(1);
    }
    var tagName = month + day + year;
    if (workDays[year + "-" + month + "-" + day]) {
        $('#divDates').append(
                '<div class="input-group ">' +
                '<div class="  btn btn-info input-group-addon">' +
                '<span class="" id="basic-addon2">' + month + ' / ' + day + ' / ' + year + '</span>' +
                '</div>' +
                '<input type="text" class="form-control" id="' + tagName + '" style="" aria-describedby="basic-addon2 ;" value ="' + workDays[year + "-" + month + "-" + day] + '" readonly>' +
                '</div>'

                );
        return value;
    } else {
        $('#divDates').append('<div class="input-group ">' +
                '<div class="  btn btn-info input-group-addon">' +
                '<span class="" id="basic-addon2">' + month + ' / ' + day + ' / ' + year + '</span>' +
                '</div>' +
                '<input type="text"  style="color:red"class="form-control"  aria-describedby="basic-addon2 ;" value ="No work schedule / Day off." readonly>' +
                '</div>');
        return value = 0;
    }
}
