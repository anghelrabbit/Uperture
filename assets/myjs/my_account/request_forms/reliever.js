/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var reliever_table = null;
var reliever_prof = '';
function setupRelievers() {
    FetchRole().done(function () {
        form_category = 5;
        tabCategory(form_category);
    });
}

function fetchEmployees() {
    $('#reliever_table').DataTable().clear().destroy();
    reliever_table = $('#reliever_table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        pageLength: 7,
        columnDefs: [
            {
                targets: [0],
                visible: false
            },
            {
                targets: 3,
                orderable: false
            }],
        ajax: {
            data: {
                structure: JSON.stringify(returnArrayStructure(select_under)),
                sched: $('input[name=reliever_date]').val()
            },
            url: 'Reliever/FetchRelievers',
            type: 'POST',
        },
    });
    $('#reliever_table_length').addClass('hidden');
    $('#reliever_table_filter').addClass('hidden');
    $('#reliever_table_info').parent().removeClass('col-sm-5');
    $('#reliever_table_paginate').parent().removeClass('col-sm-7');
    $('#reliever_table_paginate').parent().addClass('col-sm-12');
    $('#reliever_table_info').parent().empty();
    reliever_table
            .columns(0)
            .search(reliever_prof)
            .draw(false);
}

$('input[name=reliever_firstname]').on('keyup', function () {
    var lastname = '';
    if ($('input[name=reliever_firstname]').val() != '') {
        lastname = "/lastname/" + $('input[name=reliever_lastname]').val();
    }
    reliever_table
            .columns(2)
            .search('firstname/' + this.value + lastname)
            .draw();
});
$('input[name=reliever_lastname]').on('keyup', function () {
    var firstname = '';
    if ($('input[name=reliever_firstname]').val() != '') {
        firstname = "/firstname/" + $('input[name=reliever_firstname]').val();
    }
    reliever_table
            .columns(2)
            .search('lastname/' + this.value + firstname)
            .draw();
});
$('input[name=reliever_date]').change(function () {
    reliever_table
            .columns(3)
            .search($('input[name=reliever_date]').val())
            .draw();
});
$('input[name=reliever_date]').click(function (e) {
    e.stopPropagation();
});
$('input[name=reliever_lastname]').click(function (e) {
    e.stopPropagation();
});
$('input[name=reliever_firstname]').click(function (e) {
    e.stopPropagation();
});
$('span[name=reliever_close]').click(function () {
    $('div[name=modal_reliever]').modal('hide');
});

$('#reliever_table tbody').on('click', 'tr', function () {
    var selected = JSON.parse(reliever_table.row(this).data()[0]);
    var readonly = false;
    $('input[name=cs_' + 'toshift' + '_timein]').removeClass('hidden');
    $('input[name=cs_' + 'toshift' + '_timein_dayoff]').addClass('hidden');
    $('input[name=cs_' + 'toshift' + '_timeout]').removeClass('hidden');
    $('input[name=cs_' + 'toshift' + '_timeout_dayoff]').addClass('hidden');
    if (selected['is_checked'] == '') {
        reliever_prof = selected['prof'];
        if (selected['timein'] == 'Day Off') {
            toshift_dayoff = 1;
            $('input[name=cs_' + 'toshift' + '_timein]').addClass('hidden');
            $('input[name=cs_' + 'toshift' + '_timein_dayoff]').removeClass('hidden');
            $('input[name=cs_' + 'toshift' + '_timein_dayoff]').val('Day Off');
            $('input[name=cs_' + 'toshift' + '_timein]').val('');

            $('input[name=cs_' + 'toshift' + '_timeout]').addClass('hidden');
            $('input[name=cs_' + 'toshift' + '_timeout_dayoff]').removeClass('hidden');
            $('input[name=cs_' + 'toshift' + '_timeout_dayoff]').val('Day Off');
            $('input[name=cs_' + 'toshift' + '_timeout]').val('');
        } else {
                 toshift_dayoff = 0;
            $('input[name=cs_' + 'toshift' + '_timein]').val(selected['timein']);
            $('input[name=cs_' + 'toshift' + '_timein_dayoff]').val('Not Day Off');

            $('input[name=cs_' + 'toshift' + '_timeout]').val(selected['timeout']);
            $('input[name=cs_' + 'toshift' + '_timeout_dayoff]').val('Not Day Off');
        }
        $('input[name=cs_toshift_datein]').val(selected['datein']);
        $('input[name=cs_toshift_dateout]').val(selected['dateout']);
        $('input[name=cs_relievername]').val(selected['name']);

        readonly = true;
    } else {
        reliever_prof = '';
        readonly = false;
        $('input[name=cs_toshift_datein]').val('');
        $('input[name=cs_toshift_dateout]').val('');
        $('input[name=cs_relievername]').val('');
        $('input[name=cs_' + 'toshift' + '_timein]').val('');
        $('input[name=cs_' + 'toshift' + '_timeout]').val('');
    }

    $('input[name=cs_toshift_datein]').attr('readonly', readonly);
    $('input[name=cs_toshift_dateout]').attr('readonly', readonly);

    $('input[name=cs_' + 'toshift' + '_timein]').attr('readonly', readonly);
    $('input[name=cs_' + 'toshift' + '_timeout]').attr('readonly', readonly);
    $('input[name=cs_' + 'toshift' + '_timein_dayoff]').attr('readonly', readonly);
    $('input[name=cs_' + 'toshift' + '_timeout_dayoff]').attr('readonly', readonly);
    reliever_table
            .columns(0)
            .search(reliever_prof)
            .draw(false);
});