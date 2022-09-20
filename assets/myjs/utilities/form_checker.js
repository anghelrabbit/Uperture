/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function verifyform(result) {
    for (var cv = 0; cv < result.length; cv++) {
        if (result[cv]['error'] == '') {
            $(result[cv]['error_field']).addClass('hidden');
            $('input[name=' + result[cv]['input_id'] + ']').removeClass('input-error');
        } else {
//            console.log('here');
            $(result[cv]['error_field']).removeClass('hidden');
            $(result[cv]['error_field']).empty();
            $(result[cv]['error_field']).append(result[cv]['error']);
            $('input[name=' + result[cv]['input_id'] + ']').addClass('input-error');
        }
    }

}

function fetchSpecificWorkschedule(date, id, form) {
    var dfrd1 = $.Deferred();
    setTimeout(function () {
        dfrd1.resolve();
        $.ajax({
            type: 'POST',
            data: {date: date, id: id, form: form},
            url: 'RequestForms/FetchWorkschedule',
            dataType: 'json'

        }).done(function (result) {
            if (form == 1) {
                CheckSchedule(result);
            } else if (form == 2) {
                checkSelectedSchedule(result);
            } else if (form == 3) {
                CheckOvertimeSchedule(result);
            }
        });
    }, 1000);
    return $.when(dfrd1).done().promise();
}


function convert12HourFormat(time) {
    time = time.toString().match(/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [time];
    if (time.length > 1) { // If time format correct
        time = time.slice(1);  // Remove full string match value
        time[5] = +time[0] < 12 ? ' AM' : ' PM'; // Set AM/PM
        time[0] = +time[0] % 12 || 12; // Adjust hours
    }
    time.splice(3, 1);

    return time.join('');

//    return sHours + ":" + sMinutes + ":00";
}
