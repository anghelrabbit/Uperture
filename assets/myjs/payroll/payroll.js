/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function addMemberToPayroll(){
    
    $('div[name=modal_add_member_to_payroll]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
}

function setUpIntegration(){
    
    
    $('#emp_name').val('Bunny Empeynado');
    $('#emp_department').val('Marketing');
    $('#emp_job_position').val('Regular Employee');
    $('#emp_date_hired').val('11/08/2021');
    $('#emp_job_status').val('Regular/Full Time');
    $('#emp_referral_person').val('Ozang Pacatang');
    $('#emp_integration').val('Paypal');
   
    
}


function gotoPaypal(){
    window.open('https://www.paypal.com/signin');
}