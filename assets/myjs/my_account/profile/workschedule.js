$(function () {
    setupPayPeriod().done(function () {
        tabCategory();
    });

});

function tabCategory() {
    fetch_workschedule();
}

function fetch_workschedule() {
    var s_date = $('#s_date').val();
    var e_date = $('#e_date').val();

    console.log($('#s_date').val());
    $('#worksched_table').dataTable().fnDestroy();
    var table = $('#worksched_table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        pageLength: 15,
        ajax: {
            url: "WorkSchedule/FetchWorkschedule",
            type: "POST",
            data: {
                schedin: $('#worksched_in').val(),
                schedout: $('#worksched_out').val(),
                profileno: $('input[name=profileno]').val()}
        },

    });
$('#worksched_table_length').addClass('hidden');
$('#worksched_table_filter').addClass('hidden');
$('#worksched_table_info').addClass('hidden');
$('#worksched_table_paginate').addClass('hidden');
}