//var flag = 0;
//var is_clicked = 0;
//var is_notify = 0;
//$(function () {
//    allNotification();
//});
//function checkleave() {
//    window.setTimeout(check, 20000);
//
//}
//
//function gotoAnnouncementPage() {
//    $.ajax
//            ({
//                type: 'POST',
//                url: BASE_URL + "userassist/update_announcement_notification",
//                dataType: 'json'
//            })
//            .done(function (data)
//            {
//            });
//
//    $('#notification_dropdown').click();
//    $('#bell_ring').removeClass('bellring');
//    $('#bell_ring').removeClass('bellring');
//    $('#notification_warning').removeClass('pulsingbtnorange');
//    $('#notification_warning').addClass('hidden');
//    window.localStorage.setItem('notified', '1');
//    window.location.replace(BASE_URL + 'userassist/hrheaddashboard/1');
//}
//
//function check() {
//    $.ajax
//            ({
//                type: 'POST',
//                url: BASE_URL + "Leave/check_pengding_leave",
//                dataType: 'json'
//            })
//            .done(function (data)
//            {
//            });
//    checkleave();
//}
//
//function checkAnnouncement() {
//    window.setTimeout(checkAnnounce, 10000);
//
//}
//
//function checkAnnounce() {
//    $.ajax
//            ({
//                type: 'POST',
//                url: BASE_URL + "Announcement/get_urgent_announcement",
//                dataType: 'json'
//            })
//            .done(function (data)
//            {
//                if (data == true) {
//                    $('#notification_dropdown').click();
//                    $('#bell_ring').addClass('bellring');
//                    $('#bell_ring').addClass('bellring');
//                    $('#notification_warning').addClass('pulsingbtnorange');
//                    $('#notification_warning').removeClass('hidden');
//                    window.localStorage.setItem('notified', '0');
//                }
//            });
//    checkAnnouncement();
//
//}
//
//function checkNotifications() {
//    window.setTimeout(allNotification, 10000);
//}
//
//function check_evaluation() {
//    $.ajax
//            ({
//                type: 'POST',
//                url: BASE_URL + "Evaluation/CheckUnnotifiedEvaluation",
//                dataType: 'json'
//            })
//            .done(function (data)
//            {
//                $('#new_eval_notif').empty();
//                $('#new_eval_notif').append(data);
//                $('#notification_dropdown').click();
//                $('#bell_ring').addClass('bellring');
//                $('#notification_warning').addClass('pulsingbtnorange');
//                $('#notification_warning').removeClass('hidden');
//            });
//    evaluation_loop();
//}
//
//
//function allNotification() {
//    $.ajax
//            ({
//                type: 'POST',
//                url: BASE_URL + "Announcement/CheckNotification",
//                dataType: 'json'
//            })
//            .done(function (data)
//            {
//                $('#new_eval_notif').empty();
//                $('#new_announce_notif').empty();
//                $('#new_eval_notif').append(data[0]);
//                $('#new_announce_notif').append(data[1]);
//
//                if (data[0] > 0 || data[1] > 0) {
//                    $('#bell_ring').addClass('bellring');
//                    $('#notification_warning').addClass('pulsingbtnorange');
//                    $('#notification_warning').removeClass('hidden');
//                    window.localStorage.setItem('notified', '0');
//                } else {
//                    $('#bell_ring').removeClass('bellring');
//                    $('#notification_warning').removeClass('pulsingbtnorange');
//                    $('#notification_warning').addClass('hidden');
//                    window.localStorage.setItem('notified', '1');
//
//                }
//
//            });
//    checkNotifications();
//}
//
//function goToNotifPage(page) {
//    window.location.replace(BASE_URL + "userassist/" + page);
//}
//window.addEventListener('storage', function (event) {
//
//    if (event.storageArea.notified == 0) {
////        $('#notification_dropdown').click();
//        $('#bell_ring').addClass('bellring');
//        $('#notification_warning').addClass('pulsingbtnorange');
//        $('#notification_warning').removeClass('hidden');
//    } else {
//        $('#bell_ring').removeClass('bellring');
//        $('#notification_warning').removeClass('pulsingbtnorange');
//        $('#notification_warning').addClass('hidden');
//    }
//});