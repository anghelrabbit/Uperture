/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var samp = '';
var calendar = null;
cells = new Array();
var month = '';
$(function () {

    $(window).bind("resize", function () {
        if ($(this).width() < 433) {
            $('div[name=worksched_inout]').removeClass('date-changer');
        } else {
            $('div[name=worksched_inout]').removeClass('red').addClass('date-changer');
        }
    });
    setupPayPeriod().done(function () {
        tabCategory('');
    });
//    calendar = $('div[name=my_calendar]').fullCalendar({
//        header: {
//            left: 'prev,next today',
//            center: 'title',
//            right: "",
//        },
//        dayRender: function (date, cell) {
//            var month = ((date.format('M') < 10) ? "0" + date.format('M') : date.format('M'));
//            var day = ((date.format('D') < 10) ? "0" + date.format('D') : date.format('D'));
//            if (month != month) {
//                month = month;
//                cells = new Array();
//            }
//            cells[date.format('Y') + "-" + month + "-" + day] = cell;
//        },
//
//        eventAfterAllRender: function () {
//            var current_month = $('div[name=my_calendar]').fullCalendar('getDate');
//            $.ajax({
//
//                url: 'DailyTimeRecord/MySample',
//                type: 'POST',
//                data: {
//                    month: current_month.format("M"),
//                    year: current_month.format("Y")
//                },
//                dataType: 'json'
//
//            }).done(function (result) {
//
//                $.each(result, function (key, value) {
//                    if (value['date'] == '2020-05-09') {
//                        cells[value['date']].append('<div class="calendar-container" >' +
//                                '<ul style="list-style-type:none;display: table;margin: 0 auto;">' +
//                                '<li style="text-align:center;font-size:1vw">12:00 A.M. - 12:00 A.M.</li>' +
//                                '<li style="color:white;font-size:1vw">.</li>' +
//                                '<li style="text-align:center;font-size:1vw">Actual</li>' +
//                                '<li style="text-align:center;font-size:1vw">Missing IN - Missing OUT</li></ul> '
//                                + '</div>');
//                    }
//                    if (value['timein_category'] == 5) {
//                        cells[value['date']].append('<div  class="calendar-container" style="background-color:#749AC5"><label>Day Off</label></div>');
//                    } else if (value['timein_category'] == 4) {
//                        cells[value['date']].append('<div class="calendar-container" style="background-color:#6A55AE"><label>' + value['on_leave'] + '</label></div>');
//
//                    } else if (value['timein_category'] == 6) {
//
//
//                        cells[value['date']].append('<div class="calendar-container" style="background-color:#DE202E">' +
//                                '<ul style="list-style-type:none;display: table;margin: 0 auto;">' +
//                                '<li style="text-align:center;font-size:1vw">' + value['work_schedule'] + '</li>' +
//                                '<li style="color:#DE202E;font-size:1vw">.</li>' +
//                                '<li style="text-align:center;font-size:1vw">' + value['absent'] + '</li></ul> '
//                                + '</div>');
//
//                    } else {
//                        cells[value['date']].append('<div class="calendar-container" style="background-color:#3EB3A3">' +
//                                '<ul style="list-style-type:none;display: table;margin: 0 auto;">' +
//                                '<li style="text-align:center;font-size:1vw">' + value['work_schedule'] + '</li>' +
//                                '<li style="color:#3EB3A3;font-size:1vw">.</li>' +
//                                '<li style="text-align:center;font-size:1vw">Actual</li>' +
//                                '<li style="text-align:center;font-size:1vw">' + value['timein'] + " - " + value['timeout'] + '</li></ul> '
//                                + '</div>');
////                        cells[value['date']].append('<div class="schedule" style="background-color:#3EB3A3"></div></div>');
//
//                    }
//
//                });
////                cells['2020-05-01'].append('<div class="schedule"><label>Work Schedule</label><br><span>12:00 A.M - 12:00 P.M.</span></div>');
////                cells['2020-05-01'].append('<div class="schedule"><label>Actual</label><br><span>Missing In - Missing Out.</span></div>');
//            });
//        }
//
//    });

});


function fetchMyDTR() {
    $('#my_dtr_table').dataTable().fnDestroy();
    var table = $('#my_dtr_table').DataTable({
        dom: 'frtip',
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],
        "columnDefs": [
            {
                "targets": 0,
                "visible": false},
            {
                "targets": 2,
                "visible": false},
            {
                "targets": 5,
                "visible": false},
            {"targets": 7,
                "visible": false}
        ],
        oLanguage: {sProcessing: '<div><img class="zmdi-hc-spin" src="' + 'assets/images/logo.png' + '" style="width:40px; height:40px" alt="Drainwiz"><br><br><label>Processing Data...</label></div>'},
        ajax: {
            data: {
                datein: $('#worksched_in').val(),
                dateout: $('#worksched_out').val(),
            },
            url: "DailyTimeRecord/FetchMyDTR",
            type: "POST",
        },
        createdRow: function (row, data, dataIndex)
        {

        },
        rowCallback: function (row, data, index) {
            if (data[2] == 0) {
                $(row).find('td:eq(1)').attr('style', 'background-color:#749AC5; color: black; text-align:center');
            } else if (data[2] == 1) {
                $(row).find('td:eq(1)').attr('style', 'background-color:#3EB3A3; color: black; text-align:center');
            } else if (data[2] == 2) {
                $(row).find('td:eq(1)').attr('style', 'background-color:#FF392E; color: black; text-align:center');
            } else if (data[2] == 3) {
                $(row).find('td:eq(1)').attr('style', 'background-color:#FFB347; color: black; text-align:center');
            } else if (data[2] == 4) {
                $(row).find('td:eq(1)').attr('style', 'background-color:#6A55AE; color: white; text-align:center');
            }
            if (data[5] == 0) {
                $(row).find('td:eq(3)').attr('style', 'background-color:#749AC5; color: black; text-align:center');
            } else if (data[5] == 1) {
                $(row).find('td:eq(3)').attr('style', 'background-color:#3EB3A3; color: black; text-align:center');
            } else if (data[5] == 2) {
                $(row).find('td:eq(3)').attr('style', 'background-color:#FF392E; color: black; text-align:center');
            } else if (data[5] == 3) {
                $(row).find('td:eq(3)').attr('style', 'background-color:#FFB347; color: black; text-align:center');
            } else if (data[5] == 4) {
                $(row).find('td:eq(3)').attr('style', 'background-color:#6A55AE; color: white; text-align:center');
            }
            if (data[7] > 0) {
                $(row).find('td:eq(0)').attr('style', 'background-color:#ECA1AC;color:black');
                $(row).find('td:eq(2)').attr('style', 'background-color:#ECA1AC;color:black');
            } else {
                $(row).find('td:eq(0)').attr('style', 'background-color:#D9D9D7;color:black');
                $(row).find('td:eq(2)').attr('style', 'background-color:#D9D9D7;color:black');

            }

        },
        initComplete: function (settings, json) {
        }
    });
    $('#my_dtr_table_filter').addClass('hidden');
    $('#my_dtr_table_paginate').addClass('hidden');
    $('#my_dtr_table_info').addClass('hidden');
}


function tabCategory(category) {
    fetchMyDTR();
}

//{
//          title          : 'Schedule: 12:00 A.M. - 12:00 P.M.',
//          start          : new Date(2020, 4, 1),
//          backgroundColor: '#f56954', //red
//          borderColor    : '#f56954' //red
//        }