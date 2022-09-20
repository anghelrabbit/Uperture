
var holdEnter = 0;
var mouseMove = 0;
var isActive = 0;
var isShown = 0;
var tags = '';
var i = 1;



$(".timepicker").keydown(function (event) {
    return false;
});


function timeKeyUp(e, element_id) {
    e = e || window.event;
    var key = e.keyCode;
    if (key == 48) {
        if ($('#' + element_id).val() == '') {
            $('#' + element_id).val(time);
        }

    }
}

function timeKeyDown(element_id) {
    time = $('input[name=' + element_id + "]").val();
}


var inactive = setTimeout(function () {
    goInactive();
}, 10000);

$('*').bind('mousemove ', function () {
    tabIsActive();
});
$(window).focus(function () {
    tabIsActive();
});
$(window).blur(function () {
    tabIsActive();
});

window.onscroll = function (e) {
    tabIsActive();
};

$(document).idle({onIdle: function () {
        if (isActive == 0) {
            if (isShown == 0) {
                idleAccount();
            }
        }
    }, idle: 200000});

$(function () {
    if (isidle == 1) {
        $('#idleModal').modal('show');
    }
});


function idleAccount() {
    isShown = 1;
    $('#idleModal').modal('show');
    window.localStorage.setItem('state', '0');
    $.ajax
            ({
                type: 'POST',
                url: "Dashboard/IdleAccount",
                data: {idleacc: 1
                },
                dataType: 'json'
            })
            .done(function (data)
            {
            });
}


function tabIsActive() {
    clearTimeout(inactive);
    if (mouseMove == 0) {
        isActive = 1;
        mouseMove = 1;
        window.localStorage.setItem('mouseMoved', 1);
    }
    checkIsActive();
}

function checkIsActive() {
    inactive = setTimeout(function () {
        goInactive();
    }, 10000);
}


function goInactive() {
    isActive = 0;
    mouseMove = 0;
    window.localStorage.setItem('mouseMoved', 0);
    checkIsActive();
}

window.addEventListener('storage', function (event) {

    if (event.storageArea.state == 1) {
        $('#idleModal').modal('hide');
        isidle = 0;
        isShown = 0;
    } else {
        if (isShown == 0) {
            $('#idleModal').modal('show');
            isidle = 1;
            isShown = 1;
        }
    }

    if (event.storageArea.mouseMoved == 1) {
        isActive = 1;
        mouseMove = 1;
    } else {
        isActive = 0;
        mouseMove = 0;
    }
});



function tableInputKeyPress(e) {
    e = e || window.event;
    var key = e.keyCode;
    if (key == 13)
    {
        if (holdEnter == 0) {

            checkpassword();
            holdEnter = 1;
        }
        return false;
    }
}

function enableShake() {
    holdEnter = 0;
}


function checkpassword() {
    $.ajax
            ({
                type: 'POST',
                url: "LandingPage/checkPassword",
                data: {
                    password: $('#idlepassword').val()
                },
                dataType: 'json'
            })
            .done(function (data)
            {
                if (data) {
                    isidle = 0;
                    isShown = 0;
                    $('#idlepassword').val('');
                    $('#idleModal').modal('hide');
                    window.localStorage.setItem('state', '1');
                } else {
                    $("#forshake").effect("shake", {times:
                                3}, 10);
                }
            });
}