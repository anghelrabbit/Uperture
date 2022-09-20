var employee_schedule_table = null;
var scheds = {};
var sched_table = {};
var structure_template = $('#hidden-template').html();
$(function () {
    organizeStruct('modal_struct_holder', 'page_struct_holder', 1);
//    $('div[name=modal_dtr_emp]').modal('show');
});
$('span[name=btn_create_schedule]').click(function () {
    organizeStruct('page_struct_holder', 'modal_struct_holder', 0);
    scheds = {};
    sched_table = {};
    selected_departments = {};
    unselected_departments = {};
    selected_profileno = {};
    unselected_profileno = {};
    structure_profileno_selected = {};
    structure_profileno_unselected = {};
    schedTable();
    $('div[name=modal_scheduler]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
});


$('button[name=btn_close_modal_scheduler]').click(function () {
    organizeStruct('modal_struct_holder', 'page_struct_holder', 1);
    $('div[name=modal_scheduler]').modal('hide');

});

function organizeStruct(div_empty, div_append, category) {
    $('div[name=' + div_empty + ']').empty();
    $('div[name=' + div_append + ']').append(structure_template);
    FetchRole().done(function () {
        setupPayPeriod().done(function () {
            tabCategory(category);
            fetchEmployees(0);
        });
    });
}
function tabCategory(category) {
    if (category == 1) {
        FetchEmployeeSched();
    } else {
        fetchEmployees(0);
    }

}
$('#worksched_in, #worksched_out').change(function () {
    FetchEmployeeSched();
});
function FetchEmployeeSched() {
    $.ajax({
        type: 'POST',
        data: {
            structure: JSON.stringify(returnArrayStructure(select_under)),
        },
        url: 'ScheduleManagement/FetchSchedule',
        dataType: 'json'
    }).done(function (result) {
        console.log(result);
        Fetchchedules(result['profileno'], result['emps']);
    });
}


function Fetchchedules(profileno, emps) {
    $('#employee_schedule_table').DataTable().clear().destroy();
    employee_schedule_table = $('#employee_schedule_table').DataTable
            ({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax:
                        {
                            url: "ScheduleManagement/FetchEmployeeSchedule",
                            type: "POST",
                            data: {
                                profileno: profileno,
                                emps: JSON.stringify(emps),
                                sched_in: $('#worksched_in').val(),
                                sched_out: $('#worksched_out').val(),
                            },
                        },

                initComplete: function (settings, json)
                {

                }
            });
    $('#employee_schedule_table_filter').empty();



}

function fetchEmpDTR(profileno, flex) {
      $('div[name=modal_dtr_emp]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
   
    
     $('#emp_dtr_table').dataTable().fnDestroy();
    var table = $('#emp_dtr_table').DataTable({
        dom: 'frtip',
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],
        "columnDefs": [
            {
                "targets": [7,8,9,10,11],
                "visible": false}
        ],
        oLanguage: {sProcessing: '<div><img class="zmdi-hc-spin" src="' + 'assets/images/logo.png' + '" style="width:40px; height:40px" alt="Drainwiz"><br><br><label>Processing Data...</label></div>'},
         ajax: {
            data: {
                datein: $('#worksched_in').val(),
                dateout: $('#worksched_out').val(),
                profileno: profileno,
                flex:flex
            },
            url: "ScheduleManagement/FetchEmployeeDTR",
            type: "POST",
        },
        createdRow: function (row, data, dataIndex)
        {
            var rowin = 3;
            var rowout = 6;
            var with_background = true;
            var backgroundcolor = {0: '#749AC5', 1: '#3EB3A3', 2: '#6A55AE', 3: '#FFB347', 4: '#FF392E', 5: '#ECA1AC',6:'#808080'};
            if (data[8] != '1') {
                $('td:eq(6)', row).attr('rowspan', data[8]);
                $('td:eq(3)', row).attr('rowspan', data[8]);
            }
            if (data[3] == 'none') {
                $('td:eq(6)', row).remove();
                $('td:eq(3)', row).remove();
              with_background = false;
            }
            if (data[7] != '1') {
                $('td:eq(1)', row).attr('rowspan', data[7]);
//                $('td:eq(0)', row).attr('rowspan', data[7]);
            }
            if (data[1] == '') {
                $('td:eq(1)', row).remove();
                rowin--;
                rowout--;
            }
            if (data[9] == 0) {
                $('td:eq(3)', row).remove();
                $('td:eq(5)', row).remove();
                $('td:eq(2)', row).attr('colspan', 2);
                $('td:eq(2)', row).attr('style', 'text-align:center;font-weight:bold;background-color:' + backgroundcolor[0] + ';color:white;letter-spacing:0.5px');
                $('td:eq(4)', row).attr('colspan', 2);
                $('td:eq(4)', row).attr('style', 'text-align:center;font-weight:bold;background-color:' + backgroundcolor[0] + ';color:white;letter-spacing:0.5px');
            }else if (with_background == true) {
                $('td:eq('+rowin+')', row).attr('style', 'text-align:center;background-color:' + backgroundcolor[data[9]] + ';letter-spacing:0.5px;color:white;font-size:15px');
                $('td:eq('+rowout+')', row).attr('style', 'text-align:center;background-color:' + backgroundcolor[data[10]] + ';letter-spacing:0.5px;color:white;font-size:15px');
            }
            if(data[11] != 0){
                  $('td:eq(1)', row).attr('style', 'background-color:' + backgroundcolor[data[11]] + ';color:black;letter-spacing:0.5px;font-size:15px');
            }

        },
        rowCallback: function (row, data, index) {

        },
        initComplete: function (settings, json) {
        }
    });
    $('#emp_dtr_table_filter').addClass('hidden');
    $('#emp_dtr_table_paginate').addClass('hidden');
    $('#emp_dtr_table_info').addClass('hidden');
}