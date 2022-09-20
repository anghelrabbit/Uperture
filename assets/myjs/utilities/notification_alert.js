/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var notif = null;
$(function () {
    countNotification();
    refreshNotif();
});

function refreshNotif() {

    setTimeout(function () {
        countNotification();
        refreshNotif();
    }, 60000);
}

function countNotification() {
//    console.log(notif_tab_category == 'undefined');
    $.ajax({
        type: 'POST',
        url: 'Dashboard/FetchNotif',
        dataType: 'json'
    }).done(function (result) {
        notif = result;
        var pending_counter = notif['leave'] + notif['undertime'] + notif['cs'] + notif['ot'];
        var cancel_pending_counter = notif['cancel_leave'] + notif['cancel_undertime'] + notif['cancel_ot'];
        if (typeof notif_tab_category !== 'undefined') {
            countPendingForms(notif_tab_category);
        } else {
            countPendingForms(0);
        }
        if (pending_counter > 0 || cancel_pending_counter > 0) {
            $('span[name=pending_forms_alert]').removeClass('hidden');
        } else {
            $('span[name=pending_forms_alert]').addClass('hidden');

        }
        if (result['announcement'] > 0) {
            $('span[name=announcement_alert]').removeClass('hidden');
        } else {
            $('span[name=announcement_alert]').addClass('hidden');
        }
        if (result['announcement'] > 0 || result['announcement_images'] > 0) {
            $('span[name=dashboard_alert]').removeClass('hidden');
            $('span[name=announcement_badge]').removeClass('hidden');
            $('span[name=announcement_badge]').empty();
            $('span[name=announcement_badge]').append(result['announcement'] + result['announcement_images']);
        } else {
            $('span[name=announcement_badge]').addClass('hidden');
            $('span[name=dashboard_alert]').addClass('hidden');
        }
        
        if(result['approve_as_reliever'] >0){
            $('span[name=reliever_alert]').removeClass('hidden');
        }else {
             $('span[name=reliever_alert]').addClass('hidden');
        }

    });
}
function countPendingForms(index) {
    var pending_counter = notif['leave'] + notif['undertime'] + notif['cs'] + notif['ot'];
    var cancel_pending_counter = notif['cancel_leave'] + notif['cancel_undertime'] + notif['cancel_ot'];
    var text = '';
    if (index == 1) {
        text = 'cancel_';
    }
    $('span[name=leave_alert]').empty();
    $('span[name=undertime_alert]').empty();
    $('span[name=overtime_alert]').empty();
    $('span[name=leave_alert]').append((notif[text + 'leave'] > 0) ? notif[text + 'leave'] : '');
    $('span[name=undertime_alert]').append((notif[text + 'undertime'] > 0) ? notif[text + 'undertime'] : '');
    $('span[name=overtime_alert]').append((notif[text + 'ot'] > 0) ? notif[text + 'ot'] : '');

    if (index == 0) {
        $('span[name=cs_alert]').empty();
        $('span[name=cs_alert]').append((notif['cs'] > 0) ? notif['cs'] : '');
    }
    if (pending_counter > 0) {
        $('span[name=category_alert]').removeClass('hidden');
        $('span[name=pending_forms_count]').empty('');
        $('span[name=pending_forms_count]').append(pending_counter);
    } else {
        $('span[name=pending_forms_count]').empty('');
        $('span[name=category_alert]').addClass('hidden');

    }
    if (cancel_pending_counter > 0) {
        $('span[name=category_alert]').removeClass('hidden');
        $('span[name=cancel_forms_count]').empty('');
        $('span[name=cancel_forms_count]').append(cancel_pending_counter);
    } else {
        $('span[name=pending_forms_count]').empty('');
        $('span[name=category_alert]').addClass('hidden');

    }

}