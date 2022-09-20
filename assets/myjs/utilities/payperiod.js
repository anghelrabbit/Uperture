/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var onload_payperiod = 0;
var by_cutoff = 0;

$(function () {
});
var paymentone = 08;
var paymenttwo = 23;

function setupPayPeriod() {
    var dfrd1 = $.Deferred();
    if (by_cutoff == 0) {
        setTimeout(function () {
            dfrd1.resolve();
            var now = new Date($('#worksched_in').val());
            var dates = now.getDate();
            var year = now.getFullYear();
            var month = now.getMonth() + 1;
            if (month < 10) {
                month = "0" + month;
            }
            if (dates < paymenttwo) {
                if (dates < paymentone) {
                    month = month - 1;
                    dates = paymenttwo;
                    if (month == 0) {
                        month = 12;
                        year -= 1;
                    } else if (month < 10) {
                        month = "0" + month;
                    }
                } else {
                    if (paymentone < 10) {
                        dates = "0" + paymentone;
                    } else {
                        dates = paymentone;
                    }
                }
                $('#worksched_in').val(year + "-" + month + "-" + dates);
            } else {
                dates = paymenttwo;
                $('#worksched_in').val(year + "-" + month + "-" + dates);
            }
            now = new Date($('#worksched_in').val());
            year = now.getFullYear();
            month = now.getMonth() + 1;
            dates = now.getDate();

            var totalDays = Math.round(((new Date(year, month)) - (new Date(year, month - 1))) / 86400000);
            if (totalDays == 30 || dates < 16) {
                now.setDate(now.getDate() + 14);
            } else if (totalDays == 29) {
                now.setDate(now.getDate() + 13);

            } else if (totalDays == 28) {
                now.setDate(now.getDate() + 12);

            } else {
                now.setDate(now.getDate() + 15);
            }
            var date = now.getDate();
            var month = now.getMonth() + 1;
            if (date < 10) {
                date = "0" + date;
            }
            if (month < 10) {
                month = "0" + month;
            }

            $('#worksched_out').val(now.getFullYear() + "-" + month + "-" + date);
            if (onload_payperiod == 0) {
                onload_payperiod = 1;
            } else {
                tabCategory('');
            }
        }, 1000);
    } else {
        tabCategory('');
    }
    return $.when(dfrd1).done().promise();
}

$('input[name=cut_off_toggle]').change(function () {
    if ($('input[name=cut_off_toggle]').is(':checked')) {
        $('#worksched_out').attr('readonly', true);
        by_cutoff = 0;
        setupPayPeriod();
    } else {
        $('#worksched_out').attr('readonly', false);
        by_cutoff = 1;

    }
});

$('#worksched_out').change(function () {
    if ($('#worksched_out').val() < $('#worksched_in').val()) {
        $('#worksched_out').val($('#worksched_in').val());
    }
    tabCategory('');
});
