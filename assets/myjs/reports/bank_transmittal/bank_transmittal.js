
$(function () {

    FetchRole().done(function () {
        tabCategory();
        setupPayPeriod();
    }
    );
});
function tabCategory() {
    fetchEmployees(2);
}

function setupBankTransmittal() {
    if ($('select[name=payroll_category]').val() == 4) {
        organizeCompensation();
    } else if ($('select[name=payroll_category]').val() == 5) {
        organizeJobPostNet();
    } else {
        var redirect = 'BankTransmittal/SetupBankTransmittal';
        $('span[name=generate_excel]').empty();
        $('span[name=generate_excel]').append('On Process... <i class="fa fa-refresh fa-spin"></i>');
        $('span[name=generate_excel]').attr('readonly', true);
        if ($('select[name=payroll_category]').val() == 3) {
            redirect = 'Payslip/SetupEmployeePayslips';
        }
        $.ajax({
            type: 'POST',
            url: redirect,
            data: {
                selected_departments: JSON.stringify(selected_departments),
                unselected_departments: JSON.stringify(unselected_departments),
                selectd_profileno: JSON.stringify(selected_profileno),
                unselectd_profileno: JSON.stringify(unselected_profileno),
                worksched_from: $('#worksched_in').val(),
                worksched_to: $('#worksched_out').val(),
                category: $('select[name=payroll_category]').val()
            },
            dataType: 'json'
        }).done(function (result) {
            if (result['result']) {
                $('input[name=emps]').val(JSON.stringify(result['data']));
                if ($('select[name=payroll_category]').val() == 3) {
                    $('input[name=emps_payslip]').val(JSON.stringify(result['data']));
                }
                $('input[name=sched_in]').val($('#worksched_in').val());
                $('input[name=sched_out]').val($('#worksched_out').val());
                $('input[name=category]').val($('select[name=payroll_category]').val());
                $('div[name=modal_payroll]').modal('show');

            } else {
                swal({title: "Kindly choose employee/s",
                    text: "",
                    type: "error",
                    show: true,
                    backdrop: 'static',
                    timer: 2000,
                    showConfirmButton: false,
                    keyboard: false},
                        function ()
                        {
                            swal.close();
                        });
            }
            $('span[name=generate_excel]').empty();
            $('span[name=generate_excel]').append('Generate Report');

        });
    }
}


function organizeCompensation() {
    $('span[name=generate_excel]').empty();
    $('span[name=generate_excel]').append('On Process... <i class="fa fa-refresh fa-spin"></i>');
    $('span[name=generate_excel]').attr('readonly', true);
    $.ajax({
        type: 'POST',
        url: 'Compensation/OrganizeCompensation',
        data: {
            selected_departments: JSON.stringify(selected_departments),
            unselected_departments: JSON.stringify(unselected_departments),
            selectd_profileno: JSON.stringify(selected_profileno),
            unselectd_profileno: JSON.stringify(unselected_profileno),
            emp_restricts: JSON.stringify(emp_restrict),
            year: $('#worksched_year').val(),
            onemonth: $('select[name=onemonth_restrict]').val(),
        },
        dataType: 'json'
    }).done(function (result) {
        if (result['result']) {
            $('input[name=compensate_emps]').val(JSON.stringify(result['emp_data']));
            $('input[name=compensate_onemonth_emps]').val(JSON.stringify(result['emp_data']));
            $('div[name=modal_payroll]').modal('show');
        } else {
            swal({title: "No employee/s selected / No compensation report",
                text: "",
                type: "error",
                show: true,
                backdrop: 'static',
                timer: 2000,
                showConfirmButton: false,
                keyboard: false},
                    function ()
                    {
                        swal.close();
                    });
        }
        $('span[name=generate_excel]').empty();
        $('span[name=generate_excel]').append('Generate Report');


    });
}

function organizeJobPostNet() {
    $('span[name=generate_excel]').empty();
    $('span[name=generate_excel]').append('On Process... <i class="fa fa-refresh fa-spin"></i>');
    $('span[name=generate_excel]').attr('readonly', true);
    $.ajax({
        type: 'POST',
        url: 'Compensation/OrganizeJobPositionNet',
        data: {
            worksched_from: $('#worksched_in').val(),
            worksched_to: $('#worksched_out').val(),
        },
        dataType: 'json'
    }).done(function (result) {
        if (result['result']) {
            $('input[name=jobpos_net_emps]').val(JSON.stringify(result['emp_data']));
            $('div[name=modal_payroll]').modal('show');
        } else {
            swal({title: "No Payroll found.",
                text: "",
                type: "error",
                show: true,
                backdrop: 'static',
                timer: 2000,
                showConfirmButton: false,
                keyboard: false},
                    function ()
                    {
                        swal.close();
                    });
        }
        $('span[name=generate_excel]').empty();
        $('span[name=generate_excel]').append('Generate Report');


    });
}

$('span[name=generate_excel]').click(function (e) {
    setupBankTransmittal(e);
});



$('span[name=generate_payroll_excel]').click(function () {
    $('input[name=file_format]').val(0);
    $('input[name=prepared_by]').val($('input[name=payroll_prepared]').val());
    $('input[name=checked_by]').val($('input[name=payroll_checked]').val());
    $('input[name=noted_by]').val($('input[name=payroll_noted]').val());
    $('input[name=approved_by]').val($('input[name=payroll_approved]').val());
    if ($('select[name=payroll_category]').val() == 3) {
        $('#generate_payslips').submit();
    } else if ($('select[name=payroll_category]').val() == 4) {
        if ($('select[name=onemonth_restrict]').val() != 0) {
            $('input[name=compensate_onemonth_year]').val($('#worksched_year').val());
            $('input[name=compensate_onemonth_month]').val($('select[name=onemonth_restrict]').val());
            $('#generate_onemonth_compensation').submit();
        } else {
            $('input[name=compensate_year]').val($('#worksched_year').val());
            $('#generate_compensation').submit();
        }
    } else if ($('select[name=payroll_category]').val() == 5) {
        $('input[name=worksched_from]').val($('#worksched_in').val());
        $('input[name=worksched_to]').val($('#worksched_out').val());
        $('#generate_jobpos_net').submit();
    } else {
        $('#generate_transmital_excel').submit();

    }
});
$('span[name=generate_payroll_pdf]').click(function () {
    $('input[name=file_format]').val(1);
    $('input[name=prepared_by]').val($('input[name=payroll_prepared]').val());
    $('input[name=checked_by]').val($('input[name=payroll_checked]').val());
    $('input[name=noted_by]').val($('input[name=payroll_noted]').val());
    $('input[name=approved_by]').val($('input[name=payroll_approved]').val());
    if ($('select[name=payroll_category]').val() == 3) {
        $('#generate_payslips').submit();
    } else {
        $('#generate_transmital_excel').submit();
    }
});


$('select[name=payroll_category]').change(function () {
    $('span[name=generate_payroll_pdf]').addClass('hidden');
    $('div[name=div_sched_from], div[name=div_sched_to]').removeClass('hidden');
    $('div[name=div_year]').addClass('hidden');
    $('table[name=employees_table]').removeClass('hidden');
    $('div[name=emp_structure]').removeClass('hidden');
    $('#DataTables_Table_0_info').removeClass('hidden');
    $('#DataTables_Table_0_paginate').removeClass('hidden');
    $('span[name=btn_month_restrict]').addClass('hidden');
    if ($('select[name=payroll_category]').val() == 0) {
        $('span[name=generate_payroll_excel],span[name=generate_payroll_pdf]').removeClass('hidden');
        $('div[name=div_excel],div[name=div_pdf]').removeClass('col-lg-12 col-md-12 col-sm-12');
        $('div[name=div_excel],div[name=div_pdf]').addClass('col-lg-6 col-md-6 col-sm-6');
    } else if ($('select[name=payroll_category]').val() == 1) {
        $('span[name=generate_payroll_excel]').removeClass('hidden');
        $('div[name=div_excel]').removeClass('col-lg-6 col-md-6 col-sm-6');
        $('div[name=div_excel]').addClass('col-lg-12 col-md-12 col-sm-12');
        $('span[name=generate_payroll_pdf]').addClass('hidden');

    } else if ($('select[name=payroll_category]').val() == 3) {
        $('span[name=generate_payroll_pdf]').removeClass('hidden');
        $('div[name=div_pdf]').removeClass('col-lg-6 col-md-6 col-sm-6');
        $('div[name=div_pdf]').addClass('col-lg-12 col-md-12 col-sm-12');
        $('span[name=generate_payroll_excel]').addClass('hidden');

    } else if ($('select[name=payroll_category]').val() == 4) {
        $('span[name=generate_payroll_excel]').removeClass('hidden');
        $('div[name=div_excel]').removeClass('col-lg-6 col-md-6 col-sm-6');
        $('div[name=div_excel]').addClass('col-lg-12 col-md-12 col-sm-12');
        $('span[name=generate_payroll_pdf]').addClass('hidden');
        $('div[name=div_year]').removeClass('hidden');
        $('span[name=btn_month_restrict]').removeClass('hidden');
        $('div[name=div_sched_from], div[name=div_sched_to]').addClass('hidden');
    } else if ($('select[name=payroll_category]').val() == 5) {
        $('span[name=generate_payroll_excel]').removeClass('hidden');
        $('div[name=div_excel]').removeClass('col-lg-6 col-md-6 col-sm-6');
        $('div[name=div_excel]').addClass('col-lg-12 col-md-12 col-sm-12');
        $('span[name=generate_payroll_pdf]').addClass('hidden');
        $('div[name=div_year]').addClass('hidden');
        $('div[name=div_sched_from], div[name=div_sched_to]').removeClass('hidden');
        $('table[name=employees_table]').addClass('hidden');
        $('div[name=emp_structure]').addClass('hidden');
        $('#DataTables_Table_0_info').addClass('hidden');
        $('#DataTables_Table_0_paginate').addClass('hidden');
    }
});







//function setupBankTransmittal(e) {
//    e.preventDefault();
//    const xhr = new XMLHttpRequest();
//    var form_data = new FormData();
//    form_data.append("selected_departments", JSON.stringify(selected_departments));
//    form_data.append("unselected_departments", JSON.stringify(unselected_departments));
//    form_data.append("selectd_profileno", JSON.stringify(selected_profileno));
//    form_data.append("unselectd_profileno", JSON.stringify(unselected_profileno));
//    form_data.append("worksched_from", $('#worksched_in').val());
//    form_data.append("worksched_to", $('#worksched_out').val());
//    xhr.open("POST", "BankTransmittal/SetupBankTransmittal");
//    xhr.onreadystatechange = function () {
//        if (xhr.readyState == XMLHttpRequest.DONE) {
//            const result = JSON.parse(xhr.responseText);
//            console.log(result);
//        }
//    };
//    xhr.upload.addEventListener("progress", e => {
//        const percent = e.lengthComputable ? (e.loaded / e.total) * 100 : 0;
//
//        console.log(percent);
//    });
//    xhr.send(form_data);
//
//
//}