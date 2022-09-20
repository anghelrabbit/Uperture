/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(function () {
    FetchRole().done(function () {
        tabCategory();
    }
    );

});
function tabCategory() {
     fetchDTRLog();
}

function fetchDTRLog() {
    $('#dtr_log_table').DataTable().clear().destroy();
    $('#dtr_log_table').DataTable({
        dom: 'frtip',
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: 'DTRLog/FetchDTRLog',
            type: 'POST',
            data: {
                schedule: $('#worksched_in').val(),
                 structure: JSON.stringify(returnArrayStructure(select_under)),
            }

        },
        rowCallback: function (row, data, index) {
  $(row).find('td:eq(2)').attr('style', 'text-align:center');
  $(row).find('td:eq(4)').attr('style', 'text-align:center');
        }

    });
    $('#dtr_log_table_info').addClass('hidden');
    $('#dtr_log_table_filter').empty();
    $('#dtr_log_table_paginate').empty();
}

$('#worksched_in').change(function () {
    fetchDTRLog();
});