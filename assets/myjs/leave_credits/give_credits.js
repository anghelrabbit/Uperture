
var prev_data = null;
var prev_category = null;
function openGiveCredits() {
    page = 1;
    $('div[name=struct_holder]').empty();
    $('div[name=modal_struct_holder]').append(structure_template);
    FetchRole().done(function () {
        tabCategory(1);
    });
    $('div[name=modal_givecredits]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
    $('div[name=specified_holder]').addClass('hidden');
    $('div[name=asone_holder]').removeClass('hidden');
    $('select[name=credit_category]').val(0);
}

$("div[name=modal_givecredits]").on("hidden.bs.modal", function () {
    $('div[name=modal_struct_holder]').empty();
    $('div[name=struct_holder]').append(structure_template);
 
    FetchRole().done(function () {
        tabCategory(0);
    });
});

function openUpdateCredits(profileno) {
    $.ajax({
        type: 'POST',
        url: 'LeaveCredits/FetchSpecificEmployeeCredits',
        data: {profileno: profileno, year: 2020},
        dataType: 'json'
    }).done(function (result) {
        $('div[name=modal_update_credits]').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
        prev_category = result['category'];
        $('input[name=edit_credit_employee]').val(result['name']);
        $('input[name=edit_credit_company]').val(result['company']);
        $('input[name=edit_credit_position]').val(result['job_position']);
        $('input[name=edit_credit_service]').val(result['service']);
        $('select[name=edit_category]').val(result['category']);
        if (prev_category == 0) {
            $('div[name=edit_specified_holder]').addClass('hidden');
            $('div[name=edit_asone_holder]').removeClass('hidden');
            $('input[name=edit_asone]').val(result['all']);
        } else {
            $('div[name=edit_asone_holder]').addClass('hidden');
            $('div[name=edit_specified_holder]').removeClass('hidden');
            $('input[name=edit_vacationleave]').val(result['vacation']);
            $('input[name=edit_sickleave]').val(result['sick']);
            $('input[name=edit_others]').val(result['others']);

        }
        
        prev_data = result['prev_data'];
        console.log(prev_data);

    });

}








$('select[name=credit_category]').on('change', function () {
    if ($('select[name=credit_category]').val() == 0) {
        $('div[name=specified_holder]').addClass('hidden');
        $('div[name=asone_holder]').removeClass('hidden');
    } else {
        $('div[name=asone_holder]').addClass('hidden');
        $('div[name=specified_holder]').removeClass('hidden');

    }
});

$('select[name=edit_category]').on('change', function () {
    if ($('select[name=edit_category]').val() == 0) {
        $('div[name=edit_specified_holder]').addClass('hidden');
        $('div[name=edit_asone_holder]').removeClass('hidden');
    } else {
        $('div[name=edit_asone_holder]').addClass('hidden');
        $('div[name=edit_specified_holder]').removeClass('hidden');

    }
});



$('input[type=number]').on('change', function () {
    if ($(this).val() < 0) {
        $(this).val(0);
    }

});

function updateSpecificCredit() {
    $.ajax({
        dataType: 'json',
        type: 'POST',
        url: 'LeaveCredits/UpdateSpecificEmployeeCredits',
        data: {
            year: 2020,
            category: $('select[name=edit_category]').val(),
            vacation: $('input[name=edit_vacationleave]').val(),
            sick: $('input[name=edit_sickleave]').val(),
            others: $('input[name=edit_others]').val(),
            all: $('input[name=edit_asone]').val(),
            prev_data: JSON.stringify(prev_data),
            prev_category: prev_category
        }

    }).done(function (result) {
        if (result) {
            tabCategory(0);
            swal({title: "Success",
                text: "Leave credits updated.",
                type: "success",
                show: true,
                backdrop: 'static',
                timer: 1200,
                showConfirmButton: false,
                keyboard: false},
                    function ()
                    {
                        $('div[name=modal_update_credits]').modal('hide');
                        tabCategory(0);
                        swal.close();
                    });
        }
    });
}

function removeSpecificCredits() {
    $.ajax({
        dataType: 'json',
        type: 'POST',
        url: 'LeaveCredits/RemoveSpecificCredits',
        data: {
            prev_data: JSON.stringify(prev_data)
        }
    }).done(function (result) {
        if (result) {
            swal({title: "Success",
                text: "Leave credits removed.",
                type: "success",
                show: true,
                backdrop: 'static',
                timer: 1200,
                showConfirmButton: false,
                keyboard: false},
                    function ()
                    {
                        $('div[name=modal_update_credits]').modal('hide');
                        tabCategory(0);
                        swal.close();
                    });
        }
    });
}

$('button[name=btn_update_credits]').on('click', function () {
    updateSpecificCredit();
});

$('button[name=btn_remove_credits]').on('click', function () {
    removeSpecificCredits();
});

