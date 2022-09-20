$('button[name=add_reimbursement]').click(function () {
    
    $('div[name=reimbursement_modal]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
   
});
