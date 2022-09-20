///* 
// * To change this license header, choose License Headers in Project Properties.
// * To change this template file, choose Tools | Templates
// * and open the template in the editor.
// */
//
//
helpdesk_images = [
    {path: 'leave', images: ['leave_overview', 'leavestep1to4', 'leave_step5to7']},
    {path: 'undertime', images: ['undertime_overview', 'undertime_step1to2', 'undertime_step3to5']},
    {path: 'leave', image: 'leave_overview'},
];
//var small = '';
//var url = '';
$(function () {

});

function helpdesk_carousel(index) {
    $('div[name=helpdesk_inner]').empty();
    for (var cv = 0; cv < helpdesk_images[index]['images'].length; cv++) {
        var active = (cv == 0) ? 'active' : '';
        $('div[name=helpdesk_inner]').append('<div class="' + active + ' item" >' +
                '<div  style="margin-left: 0;height:450px;background: url(' + "'" + 'assets/uploads/helpdesk/' + helpdesk_images[index]['path'] + '/' + helpdesk_images[index]['images'][cv] + ".PNG'" + ') no-repeat center;background-size: cover;background-size:100% 100%">' +
                '</div>' +
                '</div>');
    }
}

$('span[name=leave_helpdesk], span[name=undertime_helpdesk]').click(function () {
    $('div[name=helpdesk_modal]').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
});

$('span[name=close_helpdesk_btn]').click(function () {
    $('div[name=helpdesk_modal]').modal('hide');
});