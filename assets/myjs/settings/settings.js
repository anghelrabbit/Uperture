/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(function () {

});

function openSettingModal() {
    $('#setting_modal').modal('show');
}

function uploadTextfile() {
    var fileToUpload = $('#wifi_textfile').prop('files')[0];
    var form_data = new FormData();
    form_data.append("file", fileToUpload);
    $.ajax({
        type: 'POST',
        url:  "Settings/UploadTextFile",
        data: form_data,
        contentType: false,
        cache: false,
        processData: false,
        dataType: 'json'
    }).done(function (data) {
    });
}

function clearWifiPass() {

    $.ajax({
        type: 'POST',
        data: {},
        url: 'Settings/ClearWifi',
        dataType: 'json'
    }).done(function (result) {


    });
}